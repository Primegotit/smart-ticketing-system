<?php
namespace app\models;

use app\config\Database;
use PDO;

class TicketScan {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getScansByEventId($eventId) {
        $sql = "SELECT ts.*, t.qr_code_hash, e.title as event_title, 
                u.first_name, u.last_name, u.email as attendee_email,
                staff.first_name as staff_first, staff.last_name as staff_last
                FROM ticket_scans ts
                JOIN tickets t ON ts.ticket_id = t.id
                JOIN events e ON t.event_id = e.id
                JOIN users u ON t.user_id = u.id
                JOIN users staff ON ts.scanned_by = staff.id
                WHERE t.event_id = :event_id
                ORDER BY ts.scanned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScansByEventIds($eventIds) {
        $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
        $sql = "SELECT ts.*, t.qr_code_hash, e.title as event_title, 
                u.first_name, u.last_name, u.email as attendee_email,
                staff.first_name as staff_first, staff.last_name as staff_last
                FROM ticket_scans ts
                JOIN tickets t ON ts.ticket_id = t.id
                JOIN events e ON t.event_id = e.id
                JOIN users u ON t.user_id = u.id
                JOIN users staff ON ts.scanned_by = staff.id
                WHERE t.event_id IN ($placeholders)
                ORDER BY ts.scanned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($eventIds);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($ticketId, $scannedBy, $scanResult) {
        $sql = "INSERT INTO ticket_scans (ticket_id, scanned_by, scan_result) 
                VALUES (:ticket_id, :scanned_by, :scan_result)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->bindParam(':scanned_by', $scannedBy);
        $stmt->bindParam(':scan_result', $scanResult);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error creating ticket scan: " . $e->getMessage());
            return false;
        }
    }

    public function getAllScans() {
        $sql = "SELECT ts.*, t.qr_code_hash, e.title as event_title, 
                CONCAT(u.first_name, ' ', u.last_name) as attendee_name,
                CONCAT(staff.first_name, ' ', staff.last_name) as scanner_name
                FROM ticket_scans ts
                JOIN tickets t ON ts.ticket_id = t.id
                JOIN events e ON t.event_id = e.id
                JOIN users u ON t.user_id = u.id
                JOIN users staff ON ts.scanned_by = staff.id
                ORDER BY ts.scanned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // In app/models/TicketScan.php
public function getScanById($id) {
    $sql = "SELECT ts.*, t.qr_code_hash, e.title as event_title, 
            CONCAT(u.first_name, ' ', u.last_name) as attendee_name,
            CONCAT(staff.first_name, ' ', staff.last_name) as scanner_name
            FROM ticket_scans ts
            JOIN tickets t ON ts.ticket_id = t.id
            JOIN events e ON t.event_id = e.id
            JOIN users u ON t.user_id = u.id
            JOIN users staff ON ts.scanned_by = staff.id
            WHERE ts.id = :id";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    // public function getScanById($id) {
    //     $sql = "SELECT ts.*, t.qr_code_hash, e.title as event_title, 
    //             CONCAT(u.first_name, ' ', u.last_name) as attendee_name,
    //             CONCAT(staff.first_name, ' ', staff.last_name) as scanner_name
    //             FROM ticket_scans ts
    //             JOIN tickets t ON ts.ticket_id = t.id
    //             JOIN events e ON t.event_id = e.id
    //             JOIN users u ON t.user_id = u.id
    //             JOIN users staff ON ts.scanned_by = staff.id
    //             WHERE ts.id = :id";
        
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->bindParam(':id', $id);
    //     $stmt->execute();
        
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public function getTotalScans() {
        $sql = "SELECT COUNT(*) as total FROM ticket_scans";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getScansByResult($result) {
        $sql = "SELECT COUNT(*) as count FROM ticket_scans WHERE scan_result = :result";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':result', $result);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function getScansByDateRange($startDate, $endDate) {
        $sql = "SELECT DATE(scanned_at) as scan_date, COUNT(*) as scan_count
                FROM ticket_scans 
                WHERE scanned_at BETWEEN :start_date AND :end_date
                GROUP BY DATE(scanned_at)
                ORDER BY scan_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopEventsByScans($limit = 5) {
        $sql = "SELECT e.id, e.title, COUNT(ts.id) as scan_count
                FROM ticket_scans ts
                JOIN tickets t ON ts.ticket_id = t.id
                JOIN events e ON t.event_id = e.id
                GROUP BY e.id, e.title
                ORDER BY scan_count DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSuspiciousScans() {
        $sql = "SELECT ts.*, t.qr_code_hash, e.title as event_title, 
                CONCAT(u.first_name, ' ', u.last_name) as attendee_name,
                CONCAT(staff.first_name, ' ', staff.last_name) as scanner_name
                FROM ticket_scans ts
                JOIN tickets t ON ts.ticket_id = t.id
                JOIN events e ON t.event_id = e.id
                JOIN users u ON t.user_id = u.id
                JOIN users staff ON ts.scanned_by = staff.id
                WHERE ts.scan_result = 'invalid' OR ts.scan_result = 'duplicate'
                ORDER BY ts.scanned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // In app/models/TicketScan.php
public function getScanCountByTicketId($ticketId) {
    $sql = "SELECT COUNT(*) as scan_count FROM ticket_scans WHERE ticket_id = :ticket_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':ticket_id', $ticketId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['scan_count'] ?? 0;
}

}