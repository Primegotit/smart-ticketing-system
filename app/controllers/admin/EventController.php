<?php
namespace app\controllers\admin;

use app\models\Event;
use app\models\Category;
use app\core\BaseController;
use app\core\AuthMiddleware;

class EventController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }
    
    public function index() {
        $eventModel = new Event();
        $status = $_GET['status'] ?? 'all';
        
        if ($status === 'all') {
            $events = $eventModel->getAllWithStatus();
        } else {
            $events = $eventModel->getEventsByStatus($status);
        }
        
        $statusCounts = $eventModel->getEventCountByStatus();

        $this->render('admin/events/index', [
            'title' => 'Manage Events - Smart Ticketing',
            'events' => $events,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status
        ]);
    }

    public function view($id) {
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event) {
            header('Location: ' . BASE_URL . '/admin/events?error=Event not found');
            exit;
        }

        $this->render('admin/events/view', [
            'title' => 'View Event - Smart Ticketing',
            'event' => $event
        ]);
    }

    public function approve($id) {
        $eventModel = new Event();
        $success = $eventModel->updateStatus($id, 'approved');

        if ($success) {
            header('Location: ' . BASE_URL . '/admin/events?success=Event approved successfully');
            exit;
        } else {
            header('Location: ' . BASE_URL . '/admin/events?error=Failed to approve event');
            exit;
        }
    }

    public function reject($id) {
        $eventModel = new Event();
        $success = $eventModel->updateStatus($id, 'rejected');

        if ($success) {
            header('Location: ' . BASE_URL . '/admin/events?success=Event rejected successfully');
            exit;
        } else {
            header('Location: ' . BASE_URL . '/admin/events?error=Failed to reject event');
            exit;
        }
    }

    public function disable($id) {
        $eventModel = new Event();
        $success = $eventModel->updateStatus($id, 'pending');

        if ($success) {
            header('Location: ' . BASE_URL . '/admin/events?success=Event disabled successfully');
            exit;
        } else {
            header('Location: ' . BASE_URL . '/admin/events?error=Failed to disable event');
            exit;
        }
    }
}