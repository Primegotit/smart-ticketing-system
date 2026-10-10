<?php
namespace app\controllers\organizer;

use app\models\QRCode;
use app\models\Event;
use app\models\Ticket;
use app\models\TicketScan;
use app\helpers\QrImage;
use app\core\BaseController;
use app\core\AuthMiddleware;

class ScannerController extends BaseController {

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

        $this->render('organizer/scanner/index', [
            'title' => 'QR Code Scanner - Smart Ticketing',
            'events' => $events
        ]);
    }

    public function scan() {
        $eventModel = new Event();
        $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);

        $this->render('organizer/scanner/scan', [
            'title' => 'Scan Ticket - Smart Ticketing',
            'events' => $events
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

    public function result() {
        if (!isset($_SESSION['scan_result'])) {
            $this->redirect(BASE_URL . '/organizer/scanner');
            return;
        }
        
        $scanResult = $_SESSION['scan_result'];
        unset($_SESSION['scan_result']);

        $this->render('organizer/scanner/result', [
            'title' => 'Scan Result - Smart Ticketing',
            'scan_result' => $scanResult
        ]);
    }

    public function history($ticketId = null) {
        $ticketScanModel = new TicketScan();
        $eventModel = new Event();

        $scans = [];

        if ($ticketId) {
            $ticketModel = new Ticket();
            $ticket = $ticketModel->getTicketWithDetails($ticketId);

            if (!$ticket) {
                header('Location: ' . BASE_URL . '/organizer/scanner/history?error=Ticket not found');
                exit;
            }

            // Verify the event belongs to this organizer
            $event = $eventModel->getById($ticket['event_id']);

            if (!$event || ($_SESSION['user']['role'] !== 'admin' && $event['organizer_id'] != $_SESSION['user']['id'])) {
                header('Location: ' . BASE_URL . '/organizer/scanner/history?error=Access denied');
                exit;
            }

            $scans = array_values(array_filter(
                $ticketScanModel->getScansByEventId($ticket['event_id']),
                function ($scan) use ($ticketId) {
                    return $scan['ticket_id'] == $ticketId;
                }
            ));
        } else {
            // All scans for this organizer's events
            $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);
            $eventIds = array_column($events, 'id');

            if (!empty($eventIds)) {
                $scans = $ticketScanModel->getScansByEventIds($eventIds);
            }
        }

        $this->render('organizer/scans/history', [
            'title' => 'Scan History - Smart Ticketing',
            'scans' => $scans,
            'event' => null
        ]);
    }

    public function download($ticketId = null) {
        // The form on the QR Codes page submits ?ticketId=... (not a route parameter)
        $ticketId = $ticketId ?: ($_GET['ticketId'] ?? null);

        $ticketModel = new Ticket();
        $ticket = $ticketId ? $ticketModel->getTicketWithDetails((int)$ticketId) : false;

        if (!$ticket) {
            header('Location: ' . BASE_URL . '/organizer/scanner?error=' . rawurlencode('Ticket not found'));
            exit;
        }

        // Verify the event belongs to this organizer
        $eventModel = new Event();
        $event = $eventModel->getById($ticket['event_id']);

        if (!$event || ($_SESSION['user']['role'] !== 'admin' && $event['organizer_id'] != $_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/organizer/scanner?error=' . rawurlencode('Access denied'));
            exit;
        }

        $imageData = QrImage::png($ticket['qr_code_hash'], 10, 4);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="ticket_' . (int)$ticket['id'] . '_qrcode.png"');
        header('Content-Length: ' . strlen($imageData));
        echo $imageData;
        exit;
    }
}
