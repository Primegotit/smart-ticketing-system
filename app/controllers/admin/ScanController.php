<?php
namespace app\controllers\admin;

use app\models\TicketScan;
use app\models\Event;
use app\models\QRCode;
use app\core\BaseController;
use app\core\AuthMiddleware;

class ScanController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }

    public function index() {
        $scanModel = new TicketScan();
        $scans = $scanModel->getAllScans();

        $this->render('admin/scans/index', [
            'title' => 'Scan Monitoring - Smart Ticketing',
            'scans' => $scans
        ]);
    }

    public function view($id) {
        $scanModel = new TicketScan();
        $scan = $scanModel->getScanById($id);

        if (!$scan) {
            header('Location: ' . BASE_URL . '/admin/scans?error=Scan not found');
            exit;
        }

        $this->render('admin/scans/view', [
            'title' => 'Scan Details - Smart Ticketing',
            'scan' => $scan
        ]);
    }
    
    // public function index() {
    //     $scanModel = new TicketScan();
    //     $scans = $scanModel->getAllScans();

    //     $this->render('admin/scans/index', [
    //         'title' => 'Scan Monitoring - Smart Ticketing',
    //         'scans' => $scans
    //     ]);
    // }

    // public function view($id) {
    //     $scanModel = new TicketScan();
    //     $scan = $scanModel->getScanById($id);

    //     if (!$scan) {
    //         header('Location: ' . BASE_URL . '/admin/scans?error=Scan record not found');
    //         exit;
    //     }

    //     $this->render('admin/scans/view', [
    //         'title' => 'Scan Details - Smart Ticketing',
    //         'scan' => $scan
    //     ]);
    // }

    public function analytics() {
        $scanModel = new TicketScan();
        
        // Get scan statistics
        $totalScans = $scanModel->getTotalScans();
        $validScans = $scanModel->getScansByResult('valid');
        $invalidScans = $scanModel->getScansByResult('invalid');
        $duplicateScans = $scanModel->getScansByResult('duplicate');
        
        // Get scans by day for the last 7 days
        $recentScans = $scanModel->getScansByDateRange(date('Y-m-d', strtotime('-7 days')), date('Y-m-d'));
        
        // Get top events by scans
        $topEvents = $scanModel->getTopEventsByScans(5);

        $this->render('admin/scans/analytics', [
            'title' => 'Scan Analytics - Smart Ticketing',
            'totalScans' => $totalScans,
            'validScans' => $validScans,
            'invalidScans' => $invalidScans,
            'duplicateScans' => $duplicateScans,
            'recentScans' => $recentScans,
            'topEvents' => $topEvents
        ]);
    }

    public function suspicious() {
        $scanModel = new TicketScan();
        $suspiciousScans = $scanModel->getSuspiciousScans();

        $this->render('admin/scans/suspicious', [
            'title' => 'Suspicious Scans - Smart Ticketing',
            'suspiciousScans' => $suspiciousScans
        ]);
    }

    public function export($type = 'all') {
        $scanModel = new TicketScan();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="scans_export_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($output, ['Scan ID', 'Ticket ID', 'Event', 'Attendee', 'Scanner', 'Result', 'Location', 'Scanned At']);
        
        switch ($type) {
            case 'valid':
                $scans = $scanModel->getScansByResult('valid');
                break;
            case 'invalid':
                $scans = $scanModel->getScansByResult('invalid');
                break;
            case 'duplicate':
                $scans = $scanModel->getScansByResult('duplicate');
                break;
            case 'suspicious':
                $scans = $scanModel->getSuspiciousScans();
                break;
            default:
                $scans = $scanModel->getAllScans();
        }
        
        // CSV data
        foreach ($scans as $scan) {
            fputcsv($output, [
                $scan['id'],
                $scan['ticket_id'],
                $scan['event_title'],
                $scan['attendee_name'],
                $scan['scanner_name'],
                $scan['scan_result'],
                $scan['scan_location'],
                $scan['scanned_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
}