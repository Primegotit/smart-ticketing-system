<?php
namespace app\controllers\organizer;

use app\models\Ticket;
use app\models\QRCode;
use app\models\Event;
use app\models\TicketScan;
use app\core\BaseController;
use app\core\AuthMiddleware;

class ScanController extends BaseController {

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

        $this->render('organizer/scans/index', [
            'title' => 'Ticket Scanning - Smart Ticketing',
            'events' => $events
        ]);
    }

    public function scan() {
        $this->render('organizer/scans/scan', [
            'title' => 'Scan Ticket - Smart Ticketing'
        ]);
    }

    public function processScan() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        try {
            $qrCodeModel = new QRCode();
            $result = $qrCodeModel->processScan($_POST['qr_code'] ?? '', $_SESSION['user'], $_POST['event_id'] ?? null);
        } catch (\Throwable $e) {
            error_log('Scan error: ' . $e->getMessage());
            $result = ['success' => false, 'message' => 'Server error while checking the ticket. Please try again.', 'ticket' => null, 'scan_type' => 'invalid'];
        }

        echo json_encode($result);
    }

    public function history($id = null) {
        $ticketScanModel = new TicketScan();
        $eventModel = new Event();
        
        $scans = [];
        $event = null;
        
        if ($id) {
            // Verify the event belongs to this organizer
            $event = $eventModel->getById($id);
            if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
                header('Location: ' . BASE_URL . '/organizer/scans/history?error=Event not found or access denied');
                exit;
            }

            $scans = $ticketScanModel->getScansByEventId($id);
        } else {
            // Get all events by this organizer
            $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);
            $eventIds = array_column($events, 'id');
            
            if (!empty($eventIds)) {
                $scans = $ticketScanModel->getScansByEventIds($eventIds);
            }
        }

        $this->render('organizer/scans/history', [
            'title' => 'Scan History - Smart Ticketing',
            'scans' => $scans,
            'event' => $event
        ]);
    }
}