<?php
namespace app\models;

use app\config\Database;
use PDO;

class Event {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Update the create method to accept status parameter
    public function create($organizerId, $categoryId, $title, $description, $venue, $dateTime, $eventImage, $totalTickets, $price, $status = 'pending') {
        $sql = "INSERT INTO events (organizer_id, category_id, title, description, venue, date_time, event_image, total_tickets, price, status) 
                VALUES (:organizer_id, :category_id, :title, :description, :venue, :date_time, :event_image, :total_tickets, :price, :status)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':organizer_id', $organizerId);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':venue', $venue);
        $stmt->bindParam(':date_time', $dateTime);
        $stmt->bindParam(':event_image', $eventImage);
        $stmt->bindParam(':total_tickets', $totalTickets);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':status', $status);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error creating event: " . $e->getMessage());
            return false;
        }
    }

    // public function create($organizerId, $categoryId, $title, $description, $venue, $dateTime, $eventImage, $totalTickets, $price) {
    //     $sql = "INSERT INTO events (organizer_id, category_id, title, description, venue, date_time, event_image, total_tickets, price) 
    //             VALUES (:organizer_id, :category_id, :title, :description, :venue, :date_time, :event_image, :total_tickets, :price)";

    //     $stmt = $this->db->prepare($sql);

    //     $stmt->bindParam(':organizer_id', $organizerId);
    //     $stmt->bindParam(':category_id', $categoryId);
    //     $stmt->bindParam(':title', $title);
    //     $stmt->bindParam(':description', $description);
    //     $stmt->bindParam(':venue', $venue);
    //     $stmt->bindParam(':date_time', $dateTime);
    //     $stmt->bindParam(':event_image', $eventImage);
    //     $stmt->bindParam(':total_tickets', $totalTickets);
    //     $stmt->bindParam(':price', $price);

    //     try {
    //         return $stmt->execute();
    //     } catch (\PDOException $e) {
    //         error_log("Error creating event: " . $e->getMessage());
    //         return false;
    //     }
    // }

    public function getAll() {
        $sql = "SELECT e.*, u.first_name, u.last_name, c.name as category_name 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id 
                LEFT JOIN categories c ON e.category_id = c.id 
                ORDER BY e.date_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email as organizer_email, c.name as category_name 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByOrganizer($organizerId) {
        $sql = "SELECT e.*, c.name as category_name 
                FROM events e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.organizer_id = :organizer_id 
                ORDER BY e.date_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizerId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTicketsSold($eventId, $quantity) {
        $sql = "UPDATE events SET tickets_sold = tickets_sold + :quantity WHERE id = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':event_id', $eventId);
        
        return $stmt->execute();
    }

    public function getAvailableTickets($eventId) {
        $sql = "SELECT (total_tickets - tickets_sold) as available_tickets FROM events WHERE id = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['available_tickets'] ?? 0;
    }

    public function getAllWithStatus($status = null) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email as organizer_email, c.name as category_name 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id 
                LEFT JOIN categories c ON e.category_id = c.id";
        
        if ($status) {
            $sql .= " WHERE e.status = :status";
        }
        
        $sql .= " ORDER BY e.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE events SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating event status: " . $e->getMessage());
            return false;
        }
    }

    public function getEventsByStatus($status) {
        $sql = "SELECT e.*, u.first_name, u.last_name, c.name as category_name 
                FROM events e 
                LEFT JOIN users u ON e.organizer_id = u.id 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.status = :status 
                ORDER BY e.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventCountByStatus() {
        $sql = "SELECT status, COUNT(*) as count FROM events GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $counts = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];
        
        foreach ($result as $row) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    }

    public function getDb() {
    return $this->db;
}

    public function getEventsByOrganizer($organizerId) {
        $sql = "SELECT e.*, c.name as category_name, 
                (SELECT COUNT(*) FROM tickets WHERE event_id = e.id) as tickets_sold_count,
                (SELECT SUM(t.amount) FROM transactions t 
                JOIN tickets tk ON t.id = tk.transaction_id 
                WHERE tk.event_id = e.id AND t.status = 'completed') as total_revenue
                FROM events e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.organizer_id = :organizer_id 
                ORDER BY e.date_time DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizerId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateEvent($id, $organizerId, $categoryId, $title, $description, $venue, $dateTime, $eventImage, $totalTickets, $price) {
        $sql = "UPDATE events SET 
                category_id = :category_id, 
                title = :title, 
                description = :description, 
                venue = :venue, 
                date_time = :date_time, 
                total_tickets = :total_tickets, 
                price = :price";
        
        if ($eventImage) {
            $sql .= ", event_image = :event_image";
        }
        
        $sql .= " WHERE id = :id AND organizer_id = :organizer_id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':venue', $venue);
        $stmt->bindParam(':date_time', $dateTime);
        $stmt->bindParam(':total_tickets', $totalTickets);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':organizer_id', $organizerId);
        
        if ($eventImage) {
            $stmt->bindParam(':event_image', $eventImage);
        }
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating event: " . $e->getMessage());
            return false;
        }
    }

    public function deleteEvent($id, $organizerId) {
        // Check if there are any tickets sold for this event
        $checkSql = "SELECT COUNT(*) as ticket_count FROM tickets WHERE event_id = :id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['ticket_count'] > 0) {
            return false; // Cannot delete event with tickets sold
        }
        
        $sql = "DELETE FROM events WHERE id = :id AND organizer_id = :organizer_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':organizer_id', $organizerId);
        
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error deleting event: " . $e->getMessage());
            return false;
        }
    }

    public function getEventRevenue($eventId) {
        $sql = "SELECT 
                COUNT(tk.id) as tickets_sold,
                SUM(t.amount) as total_revenue,
                AVG(t.amount) as average_ticket_price
                FROM tickets tk
                JOIN transactions t ON tk.transaction_id = t.id
                WHERE tk.event_id = :event_id AND t.status = 'completed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}