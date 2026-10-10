<?php
namespace app\controllers;

use app\models\Ticket;
use app\models\Event;
use app\models\QRCode;
use app\models\Transaction;
use app\helpers\QrImage;
use app\helpers\TicketPdf;
use app\core\BaseController;
use app\core\AuthMiddleware;

class TicketController extends BaseController {

    public function __construct() {
        AuthMiddleware::requireAuth();
    }

    // My Tickets list
    public function index() {
        $ticketModel = new Ticket();
        $tickets = $ticketModel->getByUserId($_SESSION['user']['id']);

        // QR images are served locally and always encode the ticket's stored hash
        $qrCodeModel = new QRCode();
        foreach ($tickets as &$ticket) {
            $qrCode = $qrCodeModel->generateForTicket($ticket['id'], [], $ticket['qr_code_hash']);
            $ticket['qr_code_url'] = $qrCode['url'];
        }
        unset($ticket);

        $this->render('tickets/index', [
            'title' => 'My Tickets - Smart Ticketing',
            'tickets' => $tickets
        ]);
    }

    public function show($id) {
        $ticketModel = new Ticket();
        $ticket = $ticketModel->getTicketWithDetails($id);

        if (!$ticket || $ticket['user_id'] != $_SESSION['user']['id']) {
            $this->render('errors/404', [
                'title' => 'Ticket Not Found - Smart Ticketing'
            ]);
            return;
        }

        // QR code for this ticket (encodes the stored hash, shown below the QR as well)
        $qrCodeModel = new QRCode();
        $qrCode = $qrCodeModel->generateForTicket($ticket['id'], [], $ticket['qr_code_hash']);

        $this->render('tickets/show', [
            'title' => 'Ticket Details - Smart Ticketing',
            'ticket' => $ticket,
            'qrCode' => $qrCode
        ]);
    }

    public function purchase($id) {
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event) {
            $this->render('errors/404', [
                'title' => 'Event Not Found - Smart Ticketing'
            ]);
            return;
        }

        // Check if tickets are available
        $availableTickets = $eventModel->getAvailableTickets($id);
        if ($availableTickets <= 0) {
            $this->render('events/show', [
                'title' => $event['title'] . ' - Smart Ticketing',
                'event' => $event,
                'error' => 'Sorry, this event is sold out.'
            ]);
            return;
        }

        // Create transaction
        $transactionModel = new Transaction();
        $transactionId = $transactionModel->create($_SESSION['user']['id'], $event['price'], 'completed');

        if (!$transactionId) {
            $this->render('events/show', [
                'title' => $event['title'] . ' - Smart Ticketing',
                'event' => $event,
                'error' => 'Failed to process payment. Please try again.'
            ]);
            return;
        }

        // Create the ticket together with its final QR code hash
        $ticketModel = new Ticket();
        $created = $ticketModel->createWithHash($id, $_SESSION['user']['id'], $transactionId);

        if ($created) {
            // Update tickets sold count
            $eventModel->updateTicketsSold($id, 1);

            header('Location: ' . BASE_URL . '/tickets/' . $created['id'] . '?success=' . rawurlencode('Ticket purchased successfully'));
            exit;
        } else {
            // Don't leave a "completed" payment behind for a ticket that was never issued
            $transactionModel->updateStatus($transactionId, 'failed');

            $this->render('events/show', [
                'title' => $event['title'] . ' - Smart Ticketing',
                'event' => $event,
                'error' => 'Failed to purchase ticket. Please try again.'
            ]);
        }
    }

    /**
     * Check-in by hash (organizers/admins only). Uses the same routine as the scanner pages.
     */
    public function validate($qrCodeHash) {
        header('Content-Type: application/json');

        if ($_SESSION['user']['role'] !== 'organizer' && $_SESSION['user']['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'You are not allowed to validate tickets.']);
            return;
        }

        $qrCodeModel = new QRCode();
        $result = $qrCodeModel->processScan($qrCodeHash, $_SESSION['user']);

        echo json_encode($result);
    }

    /**
     * QR code image (PNG) for a ticket. Used by the ticket pages.
     */
    public function qr($id) {
        $ticket = $this->findAccessibleTicket($id);
        if (!$ticket) {
            http_response_code(404);
            exit;
        }

        $this->sendFile(QrImage::png($ticket['qr_code_hash'], 8, 4), 'image/png');
    }

    /**
     * Download the QR code as a PNG image.
     */
    public function download($id) {
        $ticket = $this->findAccessibleTicket($id);

        if (!$ticket) {
            header('Location: ' . BASE_URL . '/tickets?error=' . rawurlencode('Ticket not found or access denied'));
            exit;
        }

        $this->sendFile(QrImage::png($ticket['qr_code_hash'], 10, 4), 'image/png', 'ticket_' . (int)$ticket['id'] . '_qrcode.png');
    }

    /**
     * Download the ticket as a PDF containing the QR code AND the QR code hash.
     */
    public function pdf($id) {
        $ticket = $this->findAccessibleTicket($id);

        if (!$ticket) {
            header('Location: ' . BASE_URL . '/tickets?error=' . rawurlencode('Ticket not found or access denied'));
            exit;
        }

        $pdf = TicketPdf::ticket($ticket);
        $this->sendFile($pdf, 'application/pdf', 'ticket_' . (int)$ticket['id'] . '.pdf');
    }

    /**
     * The ticket owner, the organizer of the event, or an admin may access a ticket's files.
     */
    private function findAccessibleTicket($id) {
        $ticketModel = new Ticket();
        $ticket = $ticketModel->getTicketWithDetails((int)$id);
        if (!$ticket) {
            return false;
        }

        $user = $_SESSION['user'];
        if ($ticket['user_id'] == $user['id'] || $user['role'] === 'admin') {
            return $ticket;
        }

        if ($user['role'] === 'organizer') {
            $event = (new Event())->getById($ticket['event_id']);
            if ($event && $event['organizer_id'] == $user['id']) {
                return $ticket;
            }
        }
        return false;
    }

    private function sendFile($data, $mime, $downloadName = null) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: ' . $mime);
        if ($downloadName) {
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        }
        header('Content-Length: ' . strlen($data));
        header('Cache-Control: private, no-store');
        echo $data;
        exit;
    }
}
