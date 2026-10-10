<?php
namespace app\controllers\organizer;

use app\models\Event;
use app\models\Category;
use app\models\Ticket;
use app\core\BaseController;
use app\core\AuthMiddleware;

class EventController extends BaseController {

    public function __construct() {
        AuthMiddleware::requireAuth();
        // Only organizers and admin can access
        if ($_SESSION['user']['role'] !== 'organizer' && $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/unauthorized');
            exit;
        }
    }
    
    public function index() {
        $eventModel = new Event();
        $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);

        $this->render('organizer/events/index', [
            'title' => 'My Events - Smart Ticketing',
            'events' => $events
        ]);
    }

    public function create() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $this->render('organizer/events/create', [
            'title' => 'Create Event - Smart Ticketing',
            'categories' => $categories
        ]);
    }

    public function store() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $venue = $_POST['venue'] ?? '';
        $dateTime = $_POST['date_time'] ?? '';
        $categoryId = $_POST['category_id'] ?? '';
        $totalTickets = $_POST['total_tickets'] ?? 0;
        $price = $_POST['price'] ?? 0;

        // Debug: Log form data
        error_log("Event creation form data:");
        error_log("Title: $title");
        error_log("Venue: $venue");
        error_log("DateTime: $dateTime");
        error_log("Category ID: $categoryId");
        error_log("Total Tickets: $totalTickets");
        error_log("Price: $price");

        // Validate required fields
        if (empty($title) || empty($venue) || empty($dateTime) || empty($categoryId)) {
            $categoryModel = new Category();
            $categories = $categoryModel->getAll();
            
            $this->render('organizer/events/create', [
                'title' => 'Create Event - Smart Ticketing',
                'error' => 'Please fill in all required fields.',
                'categories' => $categories,
                'formData' => $_POST
            ]);
            return;
        }

        // Handle file upload
        $eventImage = $this->handleEventImageUpload();
        error_log("Event image path: " . ($eventImage ? $eventImage : 'No image'));

        $eventModel = new Event();
        $success = $eventModel->create(
            $_SESSION['user']['id'],
            $categoryId,
            $title,
            $description,
            $venue,
            $dateTime,
            $eventImage,
            $totalTickets,
            $price,
            'pending' // Set initial status to pending for admin approval
        );

        // Debug: Check if creation was successful
        error_log("Event creation success: " . ($success ? 'Yes' : 'No'));
        
        if (!$success) {
            // Check for database errors
            $errorInfo = $eventModel->getDb()->errorInfo();
            error_log("Database error: " . print_r($errorInfo, true));
        }

        if ($success) {
            header('Location: ' . BASE_URL . '/organizer/events?success=Event created successfully. Waiting for admin approval.');
            exit;
        } else {
            $categoryModel = new Category();
            $categories = $categoryModel->getAll();
            
            $this->render('organizer/events/create', [
                'title' => 'Create Event - Smart Ticketing',
                'error' => 'Failed to create event. Please try again. Check error logs for details.',
                'categories' => $categories,
                'formData' => $_POST
            ]);
        }
    }
}

    public function edit($id) {
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        // Check if the event belongs to the current organizer
        if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
            header('Location: ' . BASE_URL . '/organizer/events?error=Event not found or access denied');
            exit;
        }

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $this->render('organizer/events/edit', [
            'title' => 'Edit Event - Smart Ticketing',
            'event' => $event,
            'categories' => $categories
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $venue = $_POST['venue'] ?? '';
            $dateTime = $_POST['date_time'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $totalTickets = $_POST['total_tickets'] ?? 0;
            $price = $_POST['price'] ?? 0;

            // Validate required fields
            if (empty($title) || empty($venue) || empty($dateTime) || empty($categoryId)) {
                $categoryModel = new Category();
                $categories = $categoryModel->getAll();
                
                $this->render('organizer/events/edit', [
                    'title' => 'Edit Event - Smart Ticketing',
                    'error' => 'Please fill in all required fields.',
                    'categories' => $categories,
                    'event' => array_merge(['id' => $id], $_POST)
                ]);
                return;
            }

            // Handle file upload
            $eventImage = $this->handleEventImageUpload();

            $eventModel = new Event();
            $success = $eventModel->updateEvent(
                $id,
                $_SESSION['user']['id'],
                $categoryId,
                $title,
                $description,
                $venue,
                $dateTime,
                $eventImage,
                $totalTickets,
                $price
            );

            if ($success) {
                header('Location: ' . BASE_URL . '/organizer/events?success=Event updated successfully');
                exit;
            } else {
                $categoryModel = new Category();
                $categories = $categoryModel->getAll();
                
                $this->render('organizer/events/edit', [
                    'title' => 'Edit Event - Smart Ticketing',
                    'error' => 'Failed to update event. Please try again.',
                    'categories' => $categories,
                    'event' => array_merge(['id' => $id], $_POST)
                ]);
            }
        }
    }

    public function delete($id) {
        $eventModel = new Event();
        $success = $eventModel->deleteEvent($id, $_SESSION['user']['id']);

        if ($success) {
            header('Location: ' . BASE_URL . '/organizer/events?success=Event deleted successfully');
            exit;
        } else {
            header('Location: ' . BASE_URL . '/organizer/events?error=Cannot delete event. It may have tickets sold.');
            exit;
        }
    }

    public function view($id) {
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        // Check if the event belongs to the current organizer
        if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
            header('Location: ' . BASE_URL . '/organizer/events?error=Event not found or access denied');
            exit;
        }

        $ticketModel = new Ticket();
        $tickets = $ticketModel->getByEventId($id);

        $revenue = $eventModel->getEventRevenue($id);

        $this->render('organizer/events/view', [
            'title' => $event['title'] . ' - Smart Ticketing',
            'event' => $event,
            'tickets' => $tickets,
            'revenue' => $revenue
        ]);
    }

    private function handleEventImageUpload() {
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'public/uploads/event_images/';
            
            // Create directory if not exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Get file info
            $file = $_FILES['event_image'];
            $filename = basename($file['name']);
            $tmp_path = $file['tmp_name'];
            $size = $file['size'];
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp_path);
            
            if (!in_array($mime, $allowed_types)) {
                return null; // Invalid file type
            }
            
            // Validate file size (max 5MB)
            if ($size > 5242880) {
                return null; // File too large
            }
            
            // Generate unique filename
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = uniqid('event_', true) . '.' . $extension;
            $targetPath = $uploadDir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($tmp_path, $targetPath)) {
                return $targetPath; // Return the path to store in database
            }
        }
        return null;
    }
}