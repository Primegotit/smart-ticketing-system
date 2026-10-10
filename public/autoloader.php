<?php
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $class = str_replace('app\\', '', $class);
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
    
    // Normalize path for Windows
    $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
    
    if (file_exists($file)) {
        require_once $file;
    } else {
        // Detailed error message
        $message = "Autoloader error: Failed to load $class\n";
        $message .= "Expected path: $file\n";
        $message .= "Current dir: " . __DIR__ . "\n";
        error_log($message);
    }
});