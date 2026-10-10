<?php

namespace app\core;

class BaseController {

    /**
     * Renders a view with layout
     * 
     * @param string $view      View path (e.g., 'admin/dashboard' or 'front/home')
     * @param array $data       Data to pass to the view
     */
    public function render($view, $data = [])
    {
        // 1. Set root directory (2 levels up from core folder)
        $rootDir = dirname(__DIR__, 2);

        // 2. Determine layout based on view path
        $layout = 'default_layout.php'; // Default Layout
        if (strpos($view, 'admin/') === 0) {
            $layout = 'admin_layout.php';
        } elseif (strpos($view, 'organizer/') === 0) {
            $layout = 'organizer_layout.php';
        } elseif (strpos($view, 'staff/') === 0) {
            $layout = 'staff_layout.php';
        } 

        // 3. Buil full paths
        $layoutPath = $rootDir . '/app/views/layouts/' . $layout;
        $viewPath = $rootDir . '/app/views/' . $view . '.php';

         // 4. Validate files exist
        if (!file_exists($layoutPath)) die("Layout not found: $layoutPath");
        if (!file_exists($viewPath)) die("View not found: $viewPath");

        // 5. Extract data to variables
        extract($data);
        
        // 6. Capture view output
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        // 7. Load layout (which uses $content variable)
        require $layoutPath;
    }

    /**
     * Redirects to a new URL
     * 
     * @param string $url  URL to redirect to
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}