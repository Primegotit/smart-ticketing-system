<?php
namespace app\controllers\organizer;

use app\models\Event;
use app\models\Ticket;
use app\core\BaseController;
use app\core\AuthMiddleware;

class DashboardController extends BaseController {

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
        $ticketModel = new Ticket();

        $events = $eventModel->getByOrganizer($_SESSION['user']['id']);
        
        // Get ticket counts for each event
        foreach ($events as &$event) {
            $event['ticket_count'] = count($ticketModel->getByEventId($event['id']));
        }

        $this->render('organizer/dashboard', [
            'title' => 'Organizer Dashboard - Smart Ticketing',
            'events' => $events
        ]);
    }
}