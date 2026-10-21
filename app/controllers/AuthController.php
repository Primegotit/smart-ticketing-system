<?php
namespace app\controllers;

use app\models\User;
use app\core\BaseController;
use app\helpers\Validator;

class AuthController extends BaseController {

    public function redirectToLogin() {
        $this->redirect(BASE_URL . '/login');
    }

    public function login() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Set user session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'profile_picture' => $user['profile_picture']
                ];

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        $this->redirect('admin/dashboard');
                        break;
                    case 'organizer':
                        $this->redirect('organizer/dashboard');
                        break;
                    default:
                        $this->redirect('events');
                }
                return;
            } else {
                $error = "Invalid email or password";
            }
        }

        $this->render('auth/login', [
            'error' => $error,
            'title' => 'Login - Smart Ticketing'
        ]);
    }

    public function register() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'attendee';
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';

            $username = trim($username);
            $firstName = trim($firstName);
            $lastName = trim($lastName);
            if (!in_array($role, ['attendee', 'organizer'], true)) {
                $role = 'attendee';
            }

            // Validate username / first name / surname (must not be numbers only)
            $identityErrors = Validator::identityErrors($username, $firstName, $lastName);

            if (!empty($identityErrors)) {
                $error = implode('<br>', array_map('htmlspecialchars', $identityErrors));
            } else if ($password !== $confirm_password) {
                // Validate passwords match
                $error = "Passwords do not match";
            } else {
                // Check if email already exists
                $userModel = new User();
                if ($userModel->emailExists($email)) {
                    $error = "Email already registered";
                } else if ($userModel->usernameExists($username)) {
                    $error = "Username already taken";
                } else {
                    // Handle file upload
                    $profilePicture = $this->handleProfilePicUpload();

                    // Create new user
                    if ($userModel->create($username, $email, $password, $role, $firstName, $lastName, $profilePicture)) {
                        $success = "Registration successful! You can now login.";
                        // Redirect to login after successful registration
                        $this->redirect('login');
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }
            }
        }

        $this->render('auth/register', [
            'error' => $error,
            'success' => $success,
            'formData' => $_POST,
            'title' => 'Register - Smart Ticketing'
        ]);
    }

    public function logout() {
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session
        session_destroy();

        // Redirect to login page
        $this->redirect('login');
    }

    private function handleProfilePicUpload() {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'public/uploads/profile_pics/';
            
            // Create directory if not exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Get file info
            $file = $_FILES['profile_picture'];
            $filename = basename($file['name']);
            $tmp_path = $file['tmp_name'];
            $size = $file['size'];
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp_path);
            
            if (!in_array($mime, $allowed_types)) {
                return null; // Invalid file type
            }
            
            // Validate file size (max 2MB)
            if ($size > 2097152) {
                return null; // File too large
            }
            
            // Generate unique filename
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = uniqid('profile_', true) . '.' . $extension;
            $targetPath = $uploadDir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($tmp_path, $targetPath)) {
                return $targetPath; // Return the path to store in database
            }
        }
        return null;
    }
}