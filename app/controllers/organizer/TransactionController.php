<?php
namespace app\controllers\organizer;

use app\models\Transaction;
use app\models\Event;
use app\core\BaseController;
use app\core\AuthMiddleware;

class TransactionController extends BaseController {

    public function __construct() {
        AuthMiddleware::requireAuth();
        // Only organizers and admin can access
        if ($_SESSION['user']['role'] !== 'organizer' && $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/unauthorized');
            exit;
        }
    }
    
    public function index() {
        $transactionModel = new Transaction();
        $eventModel = new Event();
        
        // Get all events by this organizer
        $events = $eventModel->getEventsByOrganizer($_SESSION['user']['id']);
        
        // Get transactions for all events
        $allTransactions = [];
        $totalRevenue = 0;
        
        foreach ($events as $event) {
            $transactions = $transactionModel->getByEventId($event['id']);
            foreach ($transactions as $transaction) {
                $transaction['event_title'] = $event['title'];
                $allTransactions[] = $transaction;
                
                if ($transaction['status'] === 'completed') {
                    $totalRevenue += $transaction['amount'];
                }
            }
        }

        $this->render('organizer/transactions/index', [
            'title' => 'Transaction History - Smart Ticketing',
            'transactions' => $allTransactions,
            'events' => $events,
            'totalRevenue' => $totalRevenue
        ]);
    }

    public function view($id) {
        $transactionModel = new Transaction();
        $eventModel = new Event();
        
        // Verify the event belongs to this organizer
        $event = $eventModel->getById($id);
        if (!$event || $event['organizer_id'] != $_SESSION['user']['id']) {
            header('Location: ' . BASE_URL . '/organizer/transactions?error=Event not found or access denied');
            exit;
        }
        
        $transactions = $transactionModel->getByEventId($id);
        $eventRevenue = 0;
        
        foreach ($transactions as $transaction) {
            if ($transaction['status'] === 'completed') {
                $eventRevenue += $transaction['amount'];
            }
        }

        $this->render('organizer/transactions/view', [
            'title' => 'Transactions for ' . $event['title'] . ' - Smart Ticketing',
            'transactions' => $transactions,
            'event' => $event,
            'eventRevenue' => $eventRevenue
        ]);
    }
}