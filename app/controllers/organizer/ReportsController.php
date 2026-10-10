<?php
namespace app\controllers\organizer;

use app\models\Event;
use app\models\Ticket;
use app\models\Transaction;
use app\core\BaseController;
use app\core\AuthMiddleware;

class ReportsController extends BaseController {

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

        $this->render('organizer/reports/index', [
            'title' => 'Reports - Smart Ticketing',
            'events' => $events
        ]);
    }

    public function sales() {
        $eventModel = new Event();
        $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);
        
        // Calculate total revenue
        $totalRevenue = 0;
        $totalTicketsSold = 0;
        
        foreach ($events as &$event) {
            $eventRevenue = $event['price'] * $event['tickets_sold'];
            $event['revenue'] = $eventRevenue;
            $totalRevenue += $eventRevenue;
            $totalTicketsSold += $event['tickets_sold'];
        }

        $this->render('organizer/reports/sales', [
            'title' => 'Sales Reports - Smart Ticketing',
            'events' => $events,
            'totalRevenue' => $totalRevenue,
            'totalTicketsSold' => $totalTicketsSold
        ]);
    }

    public function attendance($id = null) {
        $eventModel = new Event();
        $ticketModel = new Ticket();
        
        $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);
        $attendanceData = [];
        
        if ($id) {
            // Verify the event belongs to this organizer
            $event = $eventModel->getById($id);
            if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
                header('Location: ' . BASE_URL . '/organizer/reports/attendance?error=Event not found or access denied');
                exit;
            }
            
            // Get detailed attendance data for this event
            $tickets = $ticketModel->getByEventId($id);
            $scannedCount = 0;
            
            foreach ($tickets as $ticket) {
                if ($ticket['is_scanned']) {
                    $scannedCount++;
                }
            }
            
            $attendanceData = [
                'event' => $event,
                'total_tickets' => count($tickets),
                'scanned_tickets' => $scannedCount,
                'attendance_rate' => count($tickets) > 0 ? round(($scannedCount / count($tickets)) * 100) : 0
            ];
        } else {
            // Get attendance summary for all events
            foreach ($events as $event) {
                $tickets = $ticketModel->getByEventId($event['id']);
                $scannedCount = 0;
                
                foreach ($tickets as $ticket) {
                    if ($ticket['is_scanned']) {
                        $scannedCount++;
                    }
                }
                
                $attendanceData[$event['id']] = [
                    'event' => $event,
                    'total_tickets' => count($tickets),
                    'scanned_tickets' => $scannedCount,
                    'attendance_rate' => count($tickets) > 0 ? round(($scannedCount / count($tickets)) * 100) : 0
                ];
            }
        }

        $this->render('organizer/reports/attendance', [
            'title' => 'Attendance Reports - Smart Ticketing',
            'events' => $events,
            'attendanceData' => $attendanceData,
            'selectedEventId' => $id
        ]);
    }

    public function event($id) {
        $eventModel = new Event();
        $ticketModel = new Ticket();
        $transactionModel = new Transaction();
        
        // Verify the event belongs to this organizer
        $event = $eventModel->getById($id);
        if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
            header('Location: ' . BASE_URL . '/organizer/reports?error=Event not found or access denied');
            exit;
        }
        
        // Get event details
        $tickets = $ticketModel->getByEventId($id);
        $transactions = $transactionModel->getByEventId($id);

        // Calculate metrics
        $scannedCount = 0;
        $revenue = 0;
        
        foreach ($tickets as $ticket) {
            if ($ticket['is_scanned']) {
                $scannedCount++;
            }
        }
        
        foreach ($transactions as $transaction) {
            if ($transaction['status'] === 'completed') {
                $revenue += $transaction['amount'];
            }
        }
        
        $attendanceRate = count($tickets) > 0 ? round(($scannedCount / count($tickets)) * 100) : 0;

        $this->render('organizer/reports/event', [
            'title' => 'Event Report - ' . $event['title'],
            'event' => $event,
            'tickets' => $tickets,
            'transactions' => $transactions,
            'scannedCount' => $scannedCount,
            'revenue' => $revenue,
            'attendanceRate' => $attendanceRate
        ]);
    }

    public function export($type, $eventId = null) {
        $eventModel = new Event();
        $ticketModel = new Ticket();
        $transactionModel = new Transaction();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . $type . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        switch ($type) {
            case 'sales':
                $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);
                
                // CSV header
                fputcsv($output, ['Event', 'Date', 'Tickets Sold', 'Total Capacity', 'Price', 'Revenue']);
                
                // CSV data
                foreach ($events as $event) {
                    $revenue = $event['price'] * $event['tickets_sold'];
                    fputcsv($output, [
                        $event['title'],
                        date('M j, Y', strtotime($event['date_time'])),
                        $event['tickets_sold'],
                        $event['total_tickets'],
                        '$' . number_format($event['price'], 2),
                        '$' . number_format($revenue, 2)
                    ]);
                }
                break;
                
            case 'attendance':
                if ($eventId) {
                    // Verify the event belongs to this organizer
                    $event = $eventModel->getById($eventId);
                    if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
                        fputcsv($output, ['Error: Event not found or access denied']);
                        break;
                    }
                    
                    $tickets = $ticketModel->getByEventId($eventId);
                    
                    // CSV header
                    fputcsv($output, ['Event', 'Attendee', 'Email', 'Ticket Status', 'Scanned']);
                    
                    // CSV data
                    foreach ($tickets as $ticket) {
                        fputcsv($output, [
                            $event['title'],
                            $ticket['first_name'] . ' ' . $ticket['last_name'],
                            $ticket['email'],
                            $ticket['is_scanned'] ? 'Scanned' : 'Not Scanned',
                            $ticket['is_scanned'] ? date('M j, Y g:i A', strtotime($ticket['updated_at'] ?? $ticket['created_at'])) : 'N/A'
                        ]);
                    }
                } else {
                    fputcsv($output, ['Error: Event ID required for attendance export']);
                }
                break;
                
            default:
                fputcsv($output, ['Error: Invalid report type']);
        }
        
        fclose($output);
        exit;
    }
}