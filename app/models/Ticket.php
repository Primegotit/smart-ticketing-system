<?php
namespace app\models;

use app\config\Database;
use PDO;

class Ticket {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($eventId, $userId, $transactionId, $qrCodeHash) {
        $sql = "INSERT INTO tickets (event_id, user_id, transaction_id, qr_code_hash) 
                VALUES (:event_id, :user_id, :transaction_id, :qr_code_hash)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':event_id', $eventId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':transaction_id', $transactionId);
        $stmt->bindParam(':qr_code_hash', $qrCodeHash);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error creating ticket: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a ticket and gives it its FINAL qr_code_hash in one step.
     * Returns ['id' => ticketId, 'hash' => qr_code_hash] or false.
     */
    public function createWithHash($eventId, $userId, $transactionId) {
        $tempHash = uniqid('temp_', true);
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO tickets (event_id, user_id, transaction_id, qr_code_hash)
                                        VALUES (:event_id, :user_id, :transaction_id, :qr_code_hash)");
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_INT);
            $stmt->bindValue(':qr_code_hash', $tempHash);
            $stmt->execute();

            $ticketId = (int)$this->db->lastInsertId();
            $hash = \app\models\QRCode::makeHash($ticketId);

            $upd = $this->db->prepare("UPDATE tickets SET qr_code_hash = :hash WHERE id = :id");
            $upd->bindValue(':hash', $hash);
            $upd->bindValue(':id', $ticketId, PDO::PARAM_INT);
            $upd->execute();

            $this->db->commit();
            return ['id' => $ticketId, 'hash' => $hash];
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error creating ticket: " . $e->getMessage());
            return false;
        }
    }

    public function getByUserId($userId) {
        $sql = "SELECT t.*, e.title as event_title, e.venue, e.date_time, e.event_image, 
                u.first_name, u.last_name, u.email
                FROM tickets t 
                LEFT JOIN events e ON t.event_id = e.id 
                LEFT JOIN users u ON t.user_id = u.id
                WHERE t.user_id = :user_id 
                ORDER BY e.date_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getByEventId($eventId) {
        $sql = "SELECT t.*, u.first_name, u.last_name, u.email 
                FROM tickets t 
                LEFT JOIN users u ON t.user_id = u.id 
                WHERE t.event_id = :event_id 
                ORDER BY t.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByQrCodeHash($qrCodeHash) {
        $sql = "SELECT t.*, e.title as event_title, e.venue, e.date_time, u.first_name, u.last_name, u.email 
                FROM tickets t 
                LEFT JOIN events e ON t.event_id = e.id 
                LEFT JOIN users u ON t.user_id = u.id 
                WHERE t.qr_code_hash = :qr_code_hash 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':qr_code_hash', $qrCodeHash);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markAsScanned($ticketId, $scannedBy) {
        // Only flips the flag if the ticket is still unused, so two scanners can
        // never both "win" on the same ticket.
        $sql = "UPDATE tickets SET is_scanned = 1 WHERE id = :ticket_id AND is_scanned = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            return false; // already scanned (or ticket does not exist)
        }

        // Then log the scan
        $sql = "INSERT INTO ticket_scans (ticket_id, scanned_by, scan_result) 
                VALUES (:ticket_id, :scanned_by, 'valid')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->bindParam(':scanned_by', $scannedBy);
        
        return $stmt->execute();
    }

    public function isScanned($ticketId) {
        $sql = "SELECT is_scanned FROM tickets WHERE id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['is_scanned'] ?? 0;
    }

    public function getById($id) {
        $sql = "SELECT t.*, e.title as event_title, e.venue, e.date_time, e.event_image 
                FROM tickets t 
                LEFT JOIN events e ON t.event_id = e.id 
                WHERE t.id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT t.*, e.title as event_title, u.first_name, u.last_name, u.email, 
                (SELECT COUNT(*) FROM ticket_scans WHERE ticket_id = t.id) as scan_count
                FROM tickets t 
                LEFT JOIN events e ON t.event_id = e.id 
                LEFT JOIN users u ON t.user_id = u.id 
                ORDER BY t.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function getAll() {
    //     $sql = "SELECT t.*, e.title as event_title, u.first_name, u.last_name, u.email 
    //             FROM tickets t 
    //             LEFT JOIN events e ON t.event_id = e.id 
    //             LEFT JOIN users u ON t.user_id = u.id 
    //             ORDER BY t.created_at DESC";
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute();
        
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    public function getCount() {
        $sql = "SELECT COUNT(*) as count FROM tickets";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function updateQRCodeHash($ticketId, $qrCodeHash) {
        $sql = "UPDATE tickets SET qr_code_hash = :qr_code_hash WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':qr_code_hash', $qrCodeHash);
        $stmt->bindParam(':id', $ticketId);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating QR code hash: " . $e->getMessage());
            return false;
        }
    }

    public function getTicketWithDetails($ticketId) {
        $sql = "SELECT t.*, e.title as event_title, e.venue, e.date_time, 
                u.first_name, u.last_name, u.email, u.profile_picture,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                (SELECT COUNT(*) FROM ticket_scans WHERE ticket_id = t.id) as scan_count,
                (SELECT MIN(scanned_at) FROM ticket_scans WHERE ticket_id = t.id AND scan_result = 'valid') as scanned_at
                FROM tickets t
                JOIN events e ON t.event_id = e.id
                JOIN users u ON t.user_id = u.id
                WHERE t.id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $ticketId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAttendanceByEventId($eventId) {
        $sql = "SELECT 
                e.title as event_title,
                COUNT(t.id) as total_tickets,
                SUM(t.is_scanned) as tickets_scanned,
                ROUND((SUM(t.is_scanned) / COUNT(t.id)) * 100, 2) as attendance_rate
                FROM tickets t
                JOIN events e ON t.event_id = e.id
                WHERE t.event_id = :event_id
                GROUP BY e.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOverallAttendance() {
        $sql = "SELECT 
                e.title as event_title,
                COUNT(t.id) as total_tickets,
                SUM(t.is_scanned) as tickets_scanned,
                ROUND((SUM(t.is_scanned) / COUNT(t.id)) * 100, 2) as attendance_rate
                FROM tickets t
                JOIN events e ON t.event_id = e.id
                GROUP BY e.id
                ORDER BY e.date_time DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function getTicketWithDetails($ticketId) {
    //     $sql = "SELECT t.*, e.title as event_title, e.venue, e.date_time, 
    //             u.first_name, u.last_name, u.email, u.profile_picture,
    //             CONCAT(u.first_name, ' ', u.last_name) as user_name
    //             FROM tickets t
    //             JOIN events e ON t.event_id = e.id
    //             JOIN users u ON t.user_id = u.id
    //             WHERE t.id = :id
    //             LIMIT 1";
        
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->bindParam(':id', $ticketId);
    //     $stmt->execute();
        
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }
}