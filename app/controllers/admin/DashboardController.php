<?php
namespace app\controllers\admin;

use app\models\User;
use app\models\Event;
use app\models\Ticket;
use app\models\Transaction;
use app\core\BaseController;
use app\core\AuthMiddleware;

class DashboardController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }
    
    public function index() {
        $userModel = new User();
        $eventModel = new Event();
        $ticketModel = new Ticket();
        $transactionModel = new Transaction();

        // Get total revenue from completed transactions
        $totalRevenue = $transactionModel->getTotalRevenue();

        $stats = [
            'total_users' => count($userModel->getAll()),
            'total_events' => count($eventModel->getAll()),
            'total_tickets' => $ticketModel->getCount(),
            'total_revenue' => $totalRevenue,
            'users_by_role' => [
                'admin' => count($userModel->getUsersByRole('admin')),
                'organizer' => count($userModel->getUsersByRole('organizer')),
                'attendee' => count($userModel->getUsersByRole('attendee'))
            ],
            'recent_activity' => $this->getRecentActivity()
        ];

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard - Smart Ticketing',
            'stats' => $stats
        ]);
    }

    private function getRecentActivity() {
        // This would typically come from a dedicated activity log model
        // For now, we'll return some sample data
        return [
            [
                'type' => 'user_registration',
                'message' => 'New user registered',
                'details' => 'John Doe signed up as an attendee',
                'time' => '2 hours ago'
            ],
            [
                'type' => 'event_creation',
                'message' => 'Event created',
                'details' => 'Summer Music Festival was created',
                'time' => '5 hours ago'
            ],
            [
                'type' => 'ticket_purchase',
                'message' => 'Ticket purchased',
                'details' => 'Alice purchased a ticket for Tech Summit',
                'time' => '1 day ago'
            ]
        ];
    }
}