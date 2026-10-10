<?php
namespace app\core;

class AuthMiddleware {
    public static function requireAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function adminOnly() {
        self::requireAuth();

        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/unauthorized');
            exit;
        }
    }

    public static function caregiverOnly() {
        self::requireAuth();

        if ($_SESSION['user']['role'] !== 'organizer' && $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/unauthorized');
            exit;
        }
    }

    public static function residentOnly() {
        self::requireAuth();

        if ($_SESSION['user']['role'] !== 'staff' && $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/unauthorized');
            exit;
        }
    }

    public static function familyOnly() {
        self::requireAuth();

        if ($_SESSION['user']['role'] !== 'family' && $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/unauthorized');
            exit;
        }
    }
}