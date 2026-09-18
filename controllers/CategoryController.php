<?php

require_once 'models/CategoryRepository.php';
require_once 'models/ProductRepository.php';

class CategoryController{
    public function index(){
        Utils::isAdmin();
        $categories = (new CategoryRepository())->findAll();

        require_once 'views/category/index.php';
    }

    public function show(){
        if(isset($_GET['id'])){
            $categorie = (new CategoryRepository())->find($_GET['id']);

            if($categorie){
                $GLOBALS['pageTitle'] = $categorie->name . ' — Closetly';
                $GLOBALS['pageDescription'] = 'Shop ' . $categorie->name . ' at Closetly.';
            }

            $productRepository = new ProductRepository();
            $productByCategory = $productRepository->attachImages($productRepository->findAllByCategory($_GET['id']));
        }
        require_once 'views/category/show.php';
    }

    public function save(){
        Utils::isAdmin();
        $category = new Category();
        $category->name = trim($_POST['name'] ?? '');
        $category->parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

        if($category->name === ''){
            $_SESSION['categoryunsaved'] = 'Category name is required';
        }elseif((new CategoryRepository())->insert($category)){
            $_SESSION['categorysaved'] = 'Category saved successfully';
        }else{
            $_SESSION['categoryunsaved'] = 'Category not saved';
        }

        header('Location: /category/index');
    }

    public function delete(){
        Utils::isAdmin();

        if(!Utils::validateCsrfToken($_POST['csrf_token'] ?? null)) return header('Location: /category/index');

        if(isset($_POST['id']) && (new CategoryRepository())->delete($_POST['id'])){
            $_SESSION['categorydeleted'] = 'Category deleted successfully';
        }else{
            $_SESSION['categoryundeleted'] = 'Category still has subcategories or products - remove those first';
        }

        header('Location: /category/index');
    }
}
