<?php
namespace app\controllers\admin;

use app\models\Transaction;
use app\models\Event;
use app\models\Ticket;
use app\core\BaseController;
use app\core\AuthMiddleware;

class TransactionController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }
    
    public function index() {
        $transactionModel = new Transaction();
        $transactions = $transactionModel->getAllWithDetails();

        // Calculate statistics
        $totalRevenue = 0;
        $completedTransactions = 0;
        $failedTransactions = 0;
        
        foreach ($transactions as $transaction) {
            if ($transaction['status'] === 'completed') {
                $totalRevenue += $transaction['amount'];
                $completedTransactions++;
            } elseif ($transaction['status'] === 'failed') {
                $failedTransactions++;
            }
        }

        $this->render('admin/transactions/index', [
            'title' => 'Transaction Monitoring - Smart Ticketing',
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'completedTransactions' => $completedTransactions,
            'failedTransactions' => $failedTransactions
        ]);
    }

    public function view($id) {
        $transactionModel = new Transaction();
        $transaction = $transactionModel->getByIdWithDetails($id);

        if (!$transaction) {
            header('Location: ' . BASE_URL . '/admin/transactions?error=Transaction not found');
            exit;
        }

        $this->render('admin/transactions/view', [
            'title' => 'Transaction Details - Smart Ticketing',
            'transaction' => $transaction
        ]);
    }

    public function suspicious() {
        $transactionModel = new Transaction();
        $suspiciousTransactions = $transactionModel->getSuspiciousTransactions();

        $this->render('admin/transactions/suspicious', [
            'title' => 'Suspicious Transactions - Smart Ticketing',
            'suspiciousTransactions' => $suspiciousTransactions
        ]);
    }

    public function analytics() {
        $transactionModel = new Transaction();
        
        // Get transaction statistics
        $totalRevenue = $transactionModel->getTotalRevenue();
        $transactionsByStatus = $transactionModel->getTransactionsByStatus();
        $recentTransactions = $transactionModel->getRecentTransactions(10);
        $topEvents = $transactionModel->getTopEventsByRevenue(5);

        $this->render('admin/transactions/analytics', [
            'title' => 'Transaction Analytics - Smart Ticketing',
            'totalRevenue' => $totalRevenue,
            'transactionsByStatus' => $transactionsByStatus,
            'recentTransactions' => $recentTransactions,
            'topEvents' => $topEvents
        ]);
    }

    public function export($type = 'all') {
        $transactionModel = new Transaction();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="transactions_export_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($output, ['Transaction ID', 'User', 'Event', 'Amount', 'Status', 'Date']);
        
        switch ($type) {
            case 'completed':
                $transactions = $transactionModel->getTransactionsByStatus('completed');
                break;
            case 'failed':
                $transactions = $transactionModel->getTransactionsByStatus('failed');
                break;
            case 'pending':
                $transactions = $transactionModel->getTransactionsByStatus('pending');
                break;
            case 'suspicious':
                $transactions = $transactionModel->getSuspiciousTransactions();
                break;
            default:
                $transactions = $transactionModel->getAllWithDetails();
        }
        
        // CSV data
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction['id'],
                $transaction['user_name'],
                $transaction['event_title'],
                '$' . number_format($transaction['amount'], 2),
                ucfirst($transaction['status']),
                date('M j, Y g:i A', strtotime($transaction['created_at']))
            ]);
        }
        
        fclose($output);
        exit;
    }
}