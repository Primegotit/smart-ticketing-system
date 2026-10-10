<?php
namespace app\controllers\admin;

use app\models\Ticket;
use app\models\Event;
use app\models\TicketScan;
use app\models\QRCode;
use app\core\BaseController;
use app\core\AuthMiddleware;

class TicketController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }
    
    public function index() {
        $ticketModel = new Ticket();
        $tickets = $ticketModel->getAll();

        $this->render('admin/tickets/index', [
            'title' => 'Ticket Oversight - Smart Ticketing',
            'tickets' => $tickets
        ]);
    }

    public function view($id) {
        $ticketModel = new Ticket();
        $ticket = $ticketModel->getTicketWithDetails($id);

        if (!$ticket) {
            header('Location: ' . BASE_URL . '/admin/tickets?error=Ticket not found');
            exit;
        }

        $qrCodeModel = new QRCode();
        $scanHistory = $qrCodeModel->getScanHistory($id);

        $this->render('admin/tickets/view', [
            'title' => 'Ticket Details - Smart Ticketing',
            'ticket' => $ticket,
            'scanHistory' => $scanHistory
        ]);
    }

    public function checkFraud($id) {
    $ticketModel = new Ticket();
    $ticket = $ticketModel->getTicketWithDetails($id);

    if (!$ticket) {
        header('Location: ' . BASE_URL . '/admin/tickets?error=Ticket not found');
        exit;
    }

    // Check for potential fraud
    $fraudIndicators = $this->checkForFraudIndicators($ticket);

    $this->render('admin/tickets/fraud-check', [
        'title' => 'Fraud Check - Smart Ticketing',
        'ticket' => $ticket,
        'fraudIndicators' => $fraudIndicators
    ]);
}

    // public function checkFraud($id) {
    //     $ticketModel = new Ticket();
    //     $ticket = $ticketModel->getById($id);

    //     if (!$ticket) {
    //         header('Location: ' . BASE_URL . '/admin/tickets?error=Ticket not found');
    //         exit;
    //     }

    //     // Check for potential fraud
    //     $fraudIndicators = $this->checkForFraudIndicators($ticket);

    //     $this->render('admin/tickets/fraud-check', [
    //         'title' => 'Fraud Check - Smart Ticketing',
    //         'ticket' => $ticket,
    //         'fraudIndicators' => $fraudIndicators
    //     ]);
    // }

    public function scanHistory($id) {
        $qrCodeModel = new QRCode();
        $scanHistory = $qrCodeModel->getScanHistory($id);

        $ticketModel = new Ticket();
        $ticket = $ticketModel->getTicketWithDetails($id);

        if (!$ticket) {
            header('Location: ' . BASE_URL . '/admin/tickets?error=Ticket not found');
            exit;
        }

        $this->render('admin/tickets/scan-history', [
            'title' => 'Scan History - Smart Ticketing',
            'ticket' => $ticket,
            'scanHistory' => $scanHistory
        ]);
    }

    private function checkForFraudIndicators($ticket) {
       $indicators = [];
    
    // Get scan count if not available
    if (!isset($ticket['scan_count'])) {
        $scanModel = new TicketScan();
        $scanCount = $scanModel->getScanCountByTicketId($ticket['id']);
        $ticket['scan_count'] = $scanCount;
    }
    
    // Check for multiple scans
    if ($ticket['is_scanned'] && $ticket['scan_count'] > 1) {
        $indicators[] = [
            'type' => 'multiple_scans',
            'message' => 'This ticket has been scanned multiple times.',
            'severity' => 'high'
        ];
    }
        
        // Check for suspicious QR code patterns
        if (preg_match('/[^a-zA-Z0-9_]/', $ticket['qr_code_hash'])) {
            $indicators[] = [
                'type' => 'suspicious_qr',
                'message' => 'QR code contains unusual characters.',
                'severity' => 'medium'
            ];
        }
        
        // Check if ticket was created after event date
        $eventModel = new Event();
        $event = $eventModel->getById($ticket['event_id']);
        
        if ($event && strtotime($ticket['created_at']) > strtotime($event['date_time'])) {
            $indicators[] = [
                'type' => 'post_event_creation',
                'message' => 'Ticket was created after the event date.',
                'severity' => 'high'
            ];
        }
        
        return $indicators;
    }
}