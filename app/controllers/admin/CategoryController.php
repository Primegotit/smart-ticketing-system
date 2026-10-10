<?php
namespace app\controllers\admin;

use app\models\Category;
use app\core\BaseController;
use app\core\AuthMiddleware;

class CategoryController extends BaseController {

    public function __construct() {
        AuthMiddleware::adminOnly();
    }
    
    public function index() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $this->render('admin/categories/index', [
            'title' => 'Manage Categories - Smart Ticketing',
            'categories' => $categories
        ]);
    }

    public function create() {
        $this->render('admin/categories/create', [
            'title' => 'Create Category - Smart Ticketing'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                $this->render('admin/categories/create', [
                    'title' => 'Create Category - Smart Ticketing',
                    'error' => 'Category name is required.',
                    'name' => $name,
                    'description' => $description
                ]);
                return;
            }

            $categoryModel = new Category();
            $success = $categoryModel->create($name, $description);

            if ($success) {
                header('Location: ' . BASE_URL . '/admin/categories?success=Category created successfully');
                exit;
            } else {
                $this->render('admin/categories/create', [
                    'title' => 'Create Category - Smart Ticketing',
                    'error' => 'Failed to create category. Please try again.',
                    'name' => $name,
                    'description' => $description
                ]);
            }
        }
    }

    public function edit($id) {
        $categoryModel = new Category();
        $category = $categoryModel->getById($id);

        if (!$category) {
            header('Location: ' . BASE_URL . '/admin/categories?error=Category not found');
            exit;
        }

        $this->render('admin/categories/edit', [
            'title' => 'Edit Category - Smart Ticketing',
            'category' => $category
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                $categoryModel = new Category();
                $category = $categoryModel->getById($id);
                
                $this->render('admin/categories/edit', [
                    'title' => 'Edit Category - Smart Ticketing',
                    'error' => 'Category name is required.',
                    'category' => array_merge($category, ['name' => $name, 'description' => $description])
                ]);
                return;
            }

            $categoryModel = new Category();
            $success = $categoryModel->update($id, $name, $description);

            if ($success) {
                header('Location: ' . BASE_URL . '/admin/categories?success=Category updated successfully');
                exit;
            } else {
                $categoryModel = new Category();
                $category = $categoryModel->getById($id);
                
                $this->render('admin/categories/edit', [
                    'title' => 'Edit Category - Smart Ticketing',
                    'error' => 'Failed to update category. Please try again.',
                    'category' => array_merge($category, ['name' => $name, 'description' => $description])
                ]);
            }
        }
    }

    public function delete($id) {
        $categoryModel = new Category();
        $success = $categoryModel->delete($id);

        if ($success) {
            header('Location: ' . BASE_URL . '/admin/categories?success=Category deleted successfully');
            exit;
        } else {
            header('Location: ' . BASE_URL . '/admin/categories?error=Cannot delete category. It may be in use by events.');
            exit;
        }
    }
}