<?php
namespace app\controllers\admin;

use app\models\User;
use app\core\BaseController;
use app\core\AuthMiddleware;
use app\helpers\Validator;

class UserController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }
    
    public function index() {
        $userModel = new User();
        $users = $userModel->getAllByAdmin();

        $this->render('admin/users/index', [
            'title' => 'User Management - Smart Ticketing',
            'users' => $users
        ]);
    }

    public function view($id) {
        $userModel = new User();
        $user = $userModel->getById($id);

        if (!$user) {
            header('Location: ' . BASE_URL . '/admin/users?error=User not found');
            exit;
        }

        // Get user's tickets and events if needed
        $ticketModel = new \app\models\Ticket();
        $userTickets = $ticketModel->getByUserId($id);
        
        $eventModel = new \app\models\Event();
        $userEvents = [];
        
        if ($user['role'] === 'organizer') {
            $userEvents = $eventModel->getByOrganizer($id);
        }

        $this->render('admin/users/view', [
            'title' => 'User Details - Smart Ticketing',
            'user' => $user,
            'tickets' => $userTickets,
            'events' => $userEvents
        ]);
    }

    public function edit($id) {
        $userModel = new User();
        $user = $userModel->getById($id);

        if (!$user) {
            header('Location: ' . BASE_URL . '/admin/users?error=User not found');
            exit;
        }

        $this->render('admin/users/edit', [
            'title' => 'Edit User - Smart Ticketing',
            'user' => $user
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $user = $userModel->getById($id);

            if (!$user) {
                header('Location: ' . BASE_URL . '/admin/users?error=User not found');
                exit;
            }

            $firstName = $_POST['first_name'] ?? $user['first_name'];
            $lastName = $_POST['last_name'] ?? $user['last_name'];
            $email = $_POST['email'] ?? $user['email'];
            $role = $_POST['role'] ?? $user['role'];
            $status = $_POST['status'] ?? $user['status'];

            // Validate input
            $errors = [];
            
            $firstName = trim($firstName);
            $lastName = trim($lastName);
            $errors = array_merge($errors, Validator::identityErrors('', $firstName, $lastName, false));
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required.';
            }
            
            if (!in_array($role, ['admin', 'organizer', 'attendee'])) {
                $errors[] = 'Invalid role specified.';
            }
            
            if (!in_array($status, ['active', 'suspended', 'pending'])) {
                $errors[] = 'Invalid status specified.';
            }

            if (!empty($errors)) {
                $this->render('admin/users/edit', [
                    'title' => 'Edit User - Smart Ticketing',
                    'user' => $user,
                    'errors' => $errors
                ]);
                return;
            }

            // Update user
            $success = $userModel->update($id, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'role' => $role,
                'status' => $status
            ]);

            if ($success) {
                header('Location: ' . BASE_URL . '/admin/users/view/' . $id . '?success=User updated successfully');
                exit;
            } else {
                $this->render('admin/users/edit', [
                    'title' => 'Edit User - Smart Ticketing',
                    'user' => $user,
                    'error' => 'Failed to update user. Please try again.'
                ]);
            }
        }
    }

    public function delete($id) {
        $userModel = new User();
        $user = $userModel->getById($id);

        if (!$user) {
            header('Location: ' . BASE_URL . '/admin/users?error=User not found');
            exit;
        }

        // Prevent self-deletion
        if ($user['id'] == $_SESSION['user']['id']) {
            header('Location: ' . BASE_URL . '/admin/users?error=You cannot delete your own account');
            exit;
        }

        $success = $userModel->delete($id);

        if ($success) {
            header('Location: ' . BASE_URL . '/admin/users?success=User deleted successfully');
            exit;
        } else {
            header('Location: ' . BASE_URL . '/admin/users?error=Failed to delete user');
            exit;
        }
    }

    public function create() {
        $this->render('admin/users/create', [
            'title' => 'Create User - Smart Ticketing'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'attendee';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate input
            $errors = [];
            
            $errors = array_merge($errors, Validator::identityErrors($username, $firstName, $lastName));
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required.';
            }
            
            if (!in_array($role, ['admin', 'organizer', 'attendee'])) {
                $errors[] = 'Invalid role specified.';
            }
            
            if (empty($password)) {
                $errors[] = 'Password is required.';
            }
            
            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            if (!empty($errors)) {
                $this->render('admin/users/create', [
                    'title' => 'Create User - Smart Ticketing',
                    'errors' => $errors,
                    'formData' => $_POST
                ]);
                return;
            }

            $userModel = new User();
            
            // Check if username already exists
            if ($userModel->usernameExists($username)) {
                $this->render('admin/users/create', [
                    'title' => 'Create User - Smart Ticketing',
                    'error' => 'Username already taken.',
                    'formData' => $_POST
                ]);
                return;
            }

            // Check if email already exists
            if ($userModel->getByEmail($email)) {
                $this->render('admin/users/create', [
                    'title' => 'Create User - Smart Ticketing',
                    'error' => 'Email already exists.',
                    'formData' => $_POST
                ]);
                return;
            }

            // Create user
            $success = $userModel->createByAdmin([
                'username' => $username,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'status' => 'active'
            ]);

            if ($success) {
                header('Location: ' . BASE_URL . '/admin/users?success=User created successfully');
                exit;
            } else {
                $this->render('admin/users/create', [
                    'title' => 'Create User - Smart Ticketing',
                    'error' => 'Failed to create user. Please try again.',
                    'formData' => $_POST
                ]);
            }
        }
    }
}