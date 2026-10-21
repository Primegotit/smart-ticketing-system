<?php
namespace app\models;

use app\config\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($username, $email, $password, $role, $firstName, $lastName, $profilePicture = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, role, first_name, last_name, profile_picture) 
                VALUES (:username, :email, :password, :role, :first_name, :last_name, :profile_picture)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':profile_picture', $profilePicture);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function usernameExists($username) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function getUsersByRole($role) {
        $sql = "SELECT * FROM users WHERE role = :role ORDER BY first_name, last_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCount() {
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function getCountByRole($role) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = :role";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // In app/models/User.php
    public function getAllByAdmin() {
        $sql = "SELECT id, username, first_name, last_name, email, role, status, created_at 
                FROM users 
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT id, first_name, last_name, email, role, status, profile_picture, created_at 
                FROM users 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email) {
        $sql = "SELECT id, first_name, last_name, email, role, status, created_at 
                FROM users 
                WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET 
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                role = :role,
                status = :status
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $id);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        // Prevent deletion if user has any events (organizer)
        $eventModel = new Event();
        $userEvents = $eventModel->getByOrganizer($id);
        if (!empty($userEvents)) {
            // Cannot delete user with organized events
            return false;
        }
        // Delete tickets associated with user (including scans)
        $ticketModel = new Ticket();
        $ticketDeleted = $ticketModel->deleteByUserId($id);
        if (!$ticketDeleted) {
            error_log("Failed to delete tickets for user ID $id");
            return false;
        }
        // Delete user record
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }


    public function createByAdmin($data) {
        $sql = "INSERT INTO users (username, first_name, last_name, email, password, role, status) 
                VALUES (:username, :first_name, :last_name, :email, :password, :role, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':status', $data['status']);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }
}