<?php
namespace app\controllers\admin;

use app\models\Event;
use app\models\Ticket;
use app\models\Transaction;
use app\models\TicketScan;
use app\models\User;
use app\core\BaseController;
use app\core\AuthMiddleware;

class ReportsController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }
    
    public function index() {
        $this->render('admin/reports/index', [
            'title' => 'Reports Dashboard - Smart Ticketing'
        ]);
    }

    public function sales() {
        $transactionModel = new Transaction();
        $eventModel = new Event();
        
        // Get sales data
        $salesData = $transactionModel->getSalesReport();
        $events = $eventModel->getAll();
        
        $this->render('admin/reports/sales', [
            'title' => 'Sales Reports - Smart Ticketing',
            'salesData' => $salesData,
            'events' => $events
        ]);
    }

    public function attendance($eventId = null) {
        $ticketModel = new Ticket();
        $eventModel = new Event();
        
        $attendanceData = [];
        $event = null;
        
        if ($eventId) {
            $event = $eventModel->getById($eventId);
            $attendanceData = $ticketModel->getAttendanceByEventId($eventId);
        } else {
            $attendanceData = $ticketModel->getOverallAttendance();
        }
        
        $events = $eventModel->getAll();
        
        $this->render('admin/reports/attendance', [
            'title' => 'Attendance Reports - Smart Ticketing',
            'attendanceData' => $attendanceData,
            'events' => $events,
            'selectedEvent' => $event
        ]);
    }

    public function event($eventId) {
        $eventModel = new Event();
        $ticketModel = new Ticket();
        $transactionModel = new Transaction();
        
        $event = $eventModel->getById($eventId);
        
        if (!$event) {
            header('Location: ' . BASE_URL . '/admin/reports?error=Event not found');
            exit;
        }
        
        $tickets = $ticketModel->getByEventId($eventId);
        $revenue = $transactionModel->getRevenueByEvent($eventId);
        $attendanceRate = count(array_filter($tickets, function($ticket) {
            return $ticket['is_scanned'];
        })) / max(1, count($tickets)) * 100;
        
        $this->render('admin/reports/event', [
            'title' => 'Event Report - ' . $event['title'],
            'event' => $event,
            'tickets' => $tickets,
            'revenue' => $revenue,
            'attendanceRate' => $attendanceRate
        ]);
    }

    public function export($type, $id = null) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . $type . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        switch ($type) {
            case 'sales':
                $transactionModel = new Transaction();
                $data = $transactionModel->getSalesReport();
                
                // Add CSV headers
                fputcsv($output, ['Date', 'Event', 'Tickets Sold', 'Revenue']);
                
                // Add data
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row['sale_date'],
                        $row['event_title'],
                        $row['tickets_sold'],
                        '$' . number_format($row['revenue'], 2)
                    ]);
                }
                break;
                
            case 'attendance':
                $ticketModel = new Ticket();
                $data = $id ? $ticketModel->getAttendanceByEventId($id) : $ticketModel->getOverallAttendance();
                
                // Add CSV headers
                fputcsv($output, ['Event', 'Total Tickets', 'Tickets Scanned', 'Attendance Rate']);
                
                // Add data
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row['event_title'],
                        $row['total_tickets'],
                        $row['tickets_scanned'],
                        number_format($row['attendance_rate'], 2) . '%'
                    ]);
                }
                break;
                
            case 'event':
                $eventModel = new Event();
                $ticketModel = new Ticket();
                $transactionModel = new Transaction();
                
                $event = $eventModel->getById($id);
                $tickets = $ticketModel->getByEventId($id);
                $revenue = $transactionModel->getRevenueByEvent($id);
                
                // Add CSV headers
                fputcsv($output, ['Event Report: ' . $event['title']]);
                fputcsv($output, []); // Empty row
                fputcsv($output, ['Total Tickets Sold:', count($tickets)]);
                fputcsv($output, ['Total Revenue:', '$' . number_format($revenue, 2)]);
                fputcsv($output, ['Attendance Rate:', number_format(
                    count(array_filter($tickets, function($ticket) {
                        return $ticket['is_scanned'];
                    })) / max(1, count($tickets)) * 100, 2) . '%'
                ]);
                fputcsv($output, []); // Empty row
                fputcsv($output, ['Ticket ID', 'Attendee', 'Email', 'Status', 'Purchase Date']);
                
                // Add ticket data
                foreach ($tickets as $ticket) {
                    fputcsv($output, [
                        $ticket['id'],
                        $ticket['first_name'] . ' ' . $ticket['last_name'],
                        $ticket['email'],
                        $ticket['is_scanned'] ? 'Scanned' : 'Not Scanned',
                        $ticket['created_at']
                    ]);
                }
                break;
        }
        
        fclose($output);
        exit;
    }
}