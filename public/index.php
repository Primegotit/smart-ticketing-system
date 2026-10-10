<?php
// Calculate base URL
$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($baseDir, '/');
define('BASE_URL', $baseUrl === '/' ? '' : $baseUrl);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader
require_once __DIR__ . '/autoloader.php';

use app\core\Router; // Fix capitalization here

// Calculate request path
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Remove script path from request URI
if (strpos($request_uri, $script_name) === 0) {
    $request_uri = substr($request_uri, strlen($script_name));
}

// Remove query string
$request_uri = explode('?', $request_uri, 2)[0];
$url = trim($request_uri, '/');

// Initialize router
$router = new Router(); // Fix capitalization here

// ============= Authentication =============
$router->get('', 'AuthController@redirectToLogin');
$router->get('login', 'AuthController@login');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@register');
$router->post('register', 'AuthController@register');
$router->get('logout', 'AuthController@logout');

// ============= Events =============
$router->get('events', 'EventController@index');
$router->get('events/create', 'EventController@create');
$router->post('events/store', 'EventController@store');
$router->get('events/{id}', 'EventController@show');

// ============= Tickets =============
$router->get('tickets', 'TicketController@index');
$router->get('tickets/{id}', 'TicketController@show');
$router->get('tickets/purchase/{id}', 'TicketController@purchase');
$router->get('tickets/download/{id}', 'TicketController@download');
$router->get('tickets/qr/{id}', 'TicketController@qr');
$router->get('tickets/pdf/{id}', 'TicketController@pdf');
$router->get('tickets/validate/{qrCodeHash}', 'TicketController@validate');

// ============= Organizer Routes =============
$router->get('organizer/dashboard', 'organizer\DashboardController@index');

// ============= Organizer Event Routes =============
$router->get('organizer/events', 'organizer\EventController@index');
$router->get('organizer/events/create', 'organizer\EventController@create');
$router->post('organizer/events/store', 'organizer\EventController@store');
$router->get('organizer/events/edit/{id}', 'organizer\EventController@edit');
$router->post('organizer/events/update/{id}', 'organizer\EventController@update');
$router->post('organizer/events/delete/{id}', 'organizer\EventController@delete');
$router->get('organizer/events/view/{id}', 'organizer\EventController@view');

// ============= Organizer Ticket Routes =============
$router->get('organizer/tickets', 'organizer\TicketController@index');
$router->get('organizer/tickets/event/{id}', 'organizer\TicketController@view');
$router->get('organizer/tickets/scan/{id}', 'organizer\TicketController@scan');

// ============= Organizer Transaction Routes =============
$router->get('organizer/transactions', 'organizer\TransactionController@index');
$router->get('organizer/transactions/event/{id}', 'organizer\TransactionController@view');

// ============= Organizer Scan Routes =============
$router->get('organizer/scans', 'organizer\ScanController@index');
$router->get('organizer/scans/scan', 'organizer\ScanController@scan');
$router->post('organizer/scans/process', 'organizer\ScanController@processScan');
$router->get('organizer/scans/history', 'organizer\ScanController@history');
$router->get('organizer/scans/history/{id}', 'organizer\ScanController@history');

// ============= Organizer Reports Routes =============
$router->get('organizer/reports', 'organizer\ReportsController@index');
$router->get('organizer/reports/sales', 'organizer\ReportsController@sales');
$router->get('organizer/reports/attendance', 'organizer\ReportsController@attendance');
$router->get('organizer/reports/attendance/{id}', 'organizer\ReportsController@attendance');
$router->get('organizer/reports/event/{id}', 'organizer\ReportsController@event');
$router->get('organizer/reports/export/{type}', 'organizer\ReportsController@export');
$router->get('organizer/reports/export/{type}/{id}', 'organizer\ReportsController@export');

// ============= Organizer Scanner Routes =============
$router->get('organizer/scanner', 'organizer\ScannerController@index');
$router->get('organizer/scanner/scan', 'organizer\ScannerController@scan');
$router->post('organizer/scanner/process', 'organizer\ScannerController@processScan');
$router->get('organizer/scanner/result', 'organizer\ScannerController@result');
$router->get('organizer/scanner/history', 'organizer\ScannerController@history');
$router->get('organizer/scanner/history/{ticketId}', 'organizer\ScannerController@history');
$router->get('organizer/scanner/download', 'organizer\ScannerController@download');

// ============= Admin Routes =============
$router->get('admin/dashboard', 'admin\DashboardController@index');

// ============= Admin Category Routes =============
$router->get('admin/categories', 'admin\CategoryController@index');
$router->get('admin/categories/create', 'admin\CategoryController@create');
$router->post('admin/categories/store', 'admin\CategoryController@store');
$router->get('admin/categories/edit/{id}', 'admin\CategoryController@edit');
$router->post('admin/categories/update/{id}', 'admin\CategoryController@update');
$router->post('admin/categories/delete/{id}', 'admin\CategoryController@delete');

// ============= Admin Event Routes =============
$router->get('admin/events', 'admin\EventController@index');
$router->get('admin/events/view/{id}', 'admin\EventController@view');
$router->get('admin/events/approve/{id}', 'admin\EventController@approve');
$router->get('admin/events/reject/{id}', 'admin\EventController@reject');
$router->get('admin/events/disable/{id}', 'admin\EventController@disable');

// ============= Admin Ticket Routes =============
$router->get('admin/tickets', 'admin\TicketController@index');
$router->get('admin/tickets/view/{id}', 'admin\TicketController@view');
$router->get('admin/tickets/check/{id}', 'admin\TicketController@checkFraud');
$router->get('admin/tickets/history/{id}', 'admin\TicketController@scanHistory');

// ============= Admin Scan Routes =============
$router->get('admin/scans', 'admin\ScanController@index');
$router->get('admin/scans/view/{id}', 'admin\ScanController@view');
$router->get('admin/scans/analytics', 'admin\ScanController@analytics');
$router->get('admin/scans/suspicious', 'admin\ScanController@suspicious');
$router->get('admin/scans/export', 'admin\ScanController@export');
$router->get('admin/scans/export/{type}', 'admin\ScanController@export');

// ============= Admin Transaction Routes =============
$router->get('admin/transactions', 'admin\TransactionController@index');
$router->get('admin/transactions/view/{id}', 'admin\TransactionController@view');
$router->get('admin/transactions/suspicious', 'admin\TransactionController@suspicious');
$router->get('admin/transactions/analytics', 'admin\TransactionController@analytics');
$router->get('admin/transactions/export', 'admin\TransactionController@export');
$router->get('admin/transactions/export/{type}', 'admin\TransactionController@export');

// ============= Admin Reports Routes =============
$router->get('admin/reports', 'admin\ReportsController@index');
$router->get('admin/reports/sales', 'admin\ReportsController@sales');
$router->get('admin/reports/attendance', 'admin\ReportsController@attendance');
$router->get('admin/reports/attendance/{id}', 'admin\ReportsController@attendance');
$router->get('admin/reports/event/{id}', 'admin\ReportsController@event');
$router->get('admin/reports/export/{type}', 'admin\ReportsController@export');
$router->get('admin/reports/export/{type}/{id}', 'admin\ReportsController@export');

// ============= Admin User Routes =============
$router->get('admin/users', 'admin\UserController@index');
$router->get('admin/users/create', 'admin\UserController@create');
$router->post('admin/users/store', 'admin\UserController@store');
$router->get('admin/users/view/{id}', 'admin\UserController@view');
$router->get('admin/users/edit/{id}', 'admin\UserController@edit');
$router->post('admin/users/update/{id}', 'admin\UserController@update');
$router->post('admin/users/delete/{id}', 'admin\UserController@delete');

$router->dispatch($url);