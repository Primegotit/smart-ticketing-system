<?php
namespace app\controllers;

use app\models\Event;
use app\models\Category;
use app\models\Ticket;
use app\core\BaseController;
use app\core\AuthMiddleware;

class EventController extends BaseController {

    public function __construct() {
        AuthMiddleware::requireAuth();
    }
    
    // In your EventController, update the index method:
public function index() {
    $eventModel = new Event();
    $allEvents = $eventModel->getAll();
    
    // Separate events into upcoming and past
    $upcomingEvents = [];
    $pastEvents = [];
    $currentTime = time();
    
    foreach ($allEvents as $event) {
        $eventTime = strtotime($event['date_time']);
        if ($eventTime >= $currentTime) {
            $upcomingEvents[] = $event;
        } else {
            $pastEvents[] = $event;
        }
    }

    $this->render('events/index', [
        'title' => 'Events - Smart Ticketing',
        'upcomingEvents' => $upcomingEvents,
        'pastEvents' => $pastEvents
    ]);
}

    public function show($id) {
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event) {
            $this->render('errors/404', [
                'title' => 'Event Not Found - Smart Ticketing'
            ]);
            return;
        }

        $this->render('events/show', [
            'title' => $event['title'] . ' - Smart Ticketing',
            'event' => $event
        ]);
    }

    public function create() {
        // Only organizers and admin can create events
        if ($_SESSION['user']['role'] !== 'organizer' && $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('events');
            return;
        }

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $this->render('events/create', [
            'title' => 'Create Event - Smart Ticketing',
            'categories' => $categories
        ]);
    }

    public function store() {
        // Only organizers and admin can create events
        if ($_SESSION['user']['role'] !== 'organizer' && $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('events');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $venue = $_POST['venue'] ?? '';
            $dateTime = $_POST['date_time'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';
            $totalTickets = $_POST['total_tickets'] ?? 0;
            $price = $_POST['price'] ?? 0;

            // Handle file upload
            $eventImage = $this->handleEventImageUpload();

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
                'pending' // Set initial status to pending
            );

            if ($success) {
                $this->redirect('organizer/dashboard');
            } else {
                $this->render('events/create', [
                    'title' => 'Create Event - Smart Ticketing',
                    'error' => 'Failed to create event. Please try again.'
                ]);
            }
        }
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
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
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