<?php
namespace app\models;

use app\config\Database;
use PDO;

class Transaction {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($userId, $amount, $status = 'pending') {
        $sql = "INSERT INTO transactions (user_id, amount, status) 
                VALUES (:user_id, :amount, :status)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':status', $status);

        try {
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error creating transaction: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($transactionId, $status) {
        $sql = "UPDATE transactions SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $transactionId);
        
        return $stmt->execute();
    }

    public function getById($id) {
        $sql = "SELECT * FROM transactions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEventId($eventId) {
        $sql = "SELECT t.*, u.first_name, u.last_name, u.email 
                FROM transactions t 
                JOIN tickets tk ON t.id = tk.transaction_id 
                JOIN users u ON t.user_id = u.id 
                WHERE tk.event_id = :event_id 
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId) {
        $sql = "SELECT t.*, e.title as event_title 
                FROM transactions t 
                LEFT JOIN tickets tk ON t.id = tk.transaction_id 
                LEFT JOIN events e ON tk.event_id = e.id 
                WHERE t.user_id = :user_id 
                ORDER BY t.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithDetails() {
        $sql = "SELECT t.*, 
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                u.email as user_email,
                e.title as event_title
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN tickets tk ON t.id = tk.transaction_id
                LEFT JOIN events e ON tk.event_id = e.id
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdWithDetails($id) {
        $sql = "SELECT t.*, 
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                u.email as user_email,
                e.title as event_title,
                e.id as event_id
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN tickets tk ON t.id = tk.transaction_id
                LEFT JOIN events e ON tk.event_id = e.id
                WHERE t.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSuspiciousTransactions() {
        $sql = "SELECT t.*, 
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                u.email as user_email,
                e.title as event_title
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN tickets tk ON t.id = tk.transaction_id
                LEFT JOIN events e ON tk.event_id = e.id
                WHERE t.status = 'failed' OR t.amount <= 0 OR t.amount > 1000
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRevenue() {
        $sql = "SELECT SUM(amount) as total_revenue FROM transactions WHERE status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_revenue'] ?? 0;
    }

    public function getTransactionsByStatus($status = null) {
        $sql = "SELECT t.*, 
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                u.email as user_email,
                e.title as event_title
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN tickets tk ON t.id = tk.transaction_id
                LEFT JOIN events e ON tk.event_id = e.id";
        
        if ($status) {
            $sql .= " WHERE t.status = :status";
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentTransactions($limit = 10) {
        $sql = "SELECT t.*, 
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                u.email as user_email,
                e.title as event_title
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN tickets tk ON t.id = tk.transaction_id
                LEFT JOIN events e ON tk.event_id = e.id
                ORDER BY t.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopEventsByRevenue($limit = 5) {
        $sql = "SELECT e.id, e.title, SUM(t.amount) as total_revenue
                FROM transactions t
                JOIN tickets tk ON t.id = tk.transaction_id
                JOIN events e ON tk.event_id = e.id
                WHERE t.status = 'completed'
                GROUP BY e.id, e.title
                ORDER BY total_revenue DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSalesReport() {
        $sql = "SELECT 
                DATE(t.created_at) as sale_date,
                e.title as event_title,
                COUNT(tk.id) as tickets_sold,
                SUM(t.amount) as revenue
                FROM transactions t
                JOIN tickets tk ON t.id = tk.transaction_id
                JOIN events e ON tk.event_id = e.id
                WHERE t.status = 'completed'
                GROUP BY DATE(t.created_at), e.id
                ORDER BY sale_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevenueByEvent($eventId) {
        $sql = "SELECT SUM(t.amount) as revenue
                FROM transactions t
                JOIN tickets tk ON t.id = tk.transaction_id
                WHERE tk.event_id = :event_id AND t.status = 'completed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['revenue'] ?? 0;
    }
}