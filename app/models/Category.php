<?php
namespace app\models;

use app\config\Database;
use PDO;

class Category {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add these methods to the Category class
    public function create($name, $description = null) {
        $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $name, $description = null) {
        $sql = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        // First check if category is being used by any events
        $checkSql = "SELECT COUNT(*) as event_count FROM events WHERE category_id = :id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['event_count'] > 0) {
            return false; // Category is in use, cannot delete
        }
        
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }
}