<?php
namespace app\controllers\organizer;

use app\models\Ticket;
use app\models\Event;
use app\core\BaseController;
use app\core\AuthMiddleware;

class TicketController extends BaseController {

    public function __construct() {
        AuthMiddleware::requireAuth();
        // Only organizers and admin can access
        if ($_SESSION['user']['role'] !== 'organizer' && $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/unauthorized');
            exit;
        }
    }
    
    public function index() {
        $ticketModel = new Ticket();
        $eventModel = new Event();
        
        // Get all events by this organizer
        $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);
        
        // Get tickets for all events
        $allTickets = [];
        foreach ($events as $event) {
            $tickets = $ticketModel->getByEventId($event['id']);
            foreach ($tickets as $ticket) {
                $ticket['event_title'] = $event['title'];
                $allTickets[] = $ticket;
            }
        }

        $this->render('organizer/tickets/index', [
            'title' => 'Ticket Management - Smart Ticketing',
            'tickets' => $allTickets,
            'events' => $events
        ]);
    }

    public function view($id) {
        $ticketModel = new Ticket();
        $eventModel = new Event();
        
        // Verify the event belongs to this organizer
        $event = $eventModel->getById($id);
        if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
            header('Location: ' . BASE_URL . '/organizer/tickets?error=Event not found or access denied');
            exit;
        }
        
        $tickets = $ticketModel->getByEventId($id);

        $this->render('organizer/tickets/view', [
            'title' => 'Tickets for ' . $event['title'] . ' - Smart Ticketing',
            'tickets' => $tickets,
            'event' => $event
        ]);
    }

    public function scan($id) {
        $ticketModel = new Ticket();
        $ticket = $ticketModel->getById($id);
        
        if (!$ticket) {
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            return;
        }
        
        // Verify the event belongs to this organizer
        $eventModel = new Event();
        $event = $eventModel->getById($ticket['event_id']);
        
        if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }
        
        // Check if ticket is already scanned
        if ($ticketModel->isScanned($id)) {
            echo json_encode(['success' => false, 'message' => 'Ticket already scanned']);
            return;
        }
        
        // Mark ticket as scanned
        $success = $ticketModel->markAsScanned($id, $_SESSION['user']['id']);
        
        if ($success) {
            echo json_encode([
                'success' => true, 
                'message' => 'Ticket scanned successfully',
                'ticket' => $ticket
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to scan ticket']);
        }
    }
}