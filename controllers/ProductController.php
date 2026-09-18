<?php

require_once 'models/ProductRepository.php';
require_once 'models/ProductImageRepository.php';
require_once 'models/CategoryRepository.php';

class ProductController
{
    public function index()
    {
        $repository = new ProductRepository();
        $products = $repository->attachImages($repository->findRandom(6));
        require_once 'views/product/index.php';
    }

    public function manage(){
        Utils::isAdminOrSeller();

        $repository = new ProductRepository();
        $isAdmin = isset($_SESSION['admin']);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        if($isAdmin){
            $products = $repository->findAll($page, $perPage);
            $total = $repository->countAll();
        }else{
            $sellerId = $_SESSION['identity']['id'];
            $products = $repository->findAllBySeller($sellerId, $page, $perPage);
            $total = $repository->countBySeller($sellerId);
        }

        $totalPages = max(1, (int) ceil($total / $perPage));
        $paginationBase = '/product/manage';

        require_once 'views/product/manage.php';
    }

    public function create(){
        Utils::isAdminOrSeller();
        $categories = (new CategoryRepository())->findAll();
        require_once 'views/product/create.php';
    }

    public function save(){
        Utils::isAdminOrSeller();

        if(!$_POST) return header('Location: /product/create');

        if(!empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['price']) && !empty($_POST['stock'])){
            $repository = new ProductRepository();

            if(isset($_GET['id'])){
                $existing = $repository->find($_GET['id']);
                if(!$this->canManage($existing)) return header('Location: /product/manage');
            }

            $product = new Product();
            $product->category_id = $_POST['category_id'];
            $product->seller_id = isset($_GET['id']) ? $existing->seller_id : ($_SESSION['identity']['id'] ?? null);
            $product->name = $_POST['name'];
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            $product->stock = $_POST['stock'];

            if(!empty($_FILES['image']['name'])){
                $filename = $this->storeUploadedImage($_FILES['image']['tmp_name']);
                if($filename) $product->image = $filename;
            }

            if(isset($_GET['id'])){
                $product->id = $_GET['id'];
                $repository->update($product);
                $productId = $_GET['id'];
                $_SESSION['productupdated'] = 'Product updated successfully';

            }else{
                $productId = $repository->insert($product);
                $_SESSION['productsaved'] = 'Product saved successfully';
            }

            if($productId && !empty($_FILES['images']['name'][0])){
                $imageRepository = new ProductImageRepository();
                foreach($_FILES['images']['tmp_name'] as $i => $tmpName){
                    if(empty($_FILES['images']['name'][$i])) continue;
                    $filename = $this->storeUploadedImage($tmpName);
                    if($filename) $imageRepository->insert($productId, $filename);
                }
            }

        }else{
            $_SESSION['productunsaved'] = 'Product not saved';
        }

        header('Location: /product/manage');

    }

    public function delete(){
        Utils::isAdminOrSeller();

        if(!Utils::validateCsrfToken($_POST['csrf_token'] ?? null)) return header('Location: /product/manage');

        if(isset($_POST['id'])){
            $repository = new ProductRepository();
            $existing = $repository->find($_POST['id']);

            if(!$this->canManage($existing)){
                header('Location: /product/manage');
                return;
            }

            $deleted = $repository->delete($_POST['id']);

            if($deleted){
                $_SESSION['productdeleted'] = 'Product deleted successfully';
            }else{
                $_SESSION['productundeleted'] = 'Product not deleted';
            }
        }else{
            $_SESSION['productundeleted'] = 'Product not deleted';
        }

        header('Location: /product/manage');
    }

    public function update(){
        Utils::isAdminOrSeller();
        $edit = true;

        if(isset($_GET['id'])){
            $repository = new ProductRepository();
            $getOneProduct = $repository->find($_GET['id']);
            if(!$this->canManage($getOneProduct)){
                header('Location: /product/manage');
                return;
            }
            $repository->attachImages([$getOneProduct]);
        }else{
            $_SESSION['productnotfound'] = 'Product not found';
            header('Location: /product/manage');
            return;
        }
        $categories = (new CategoryRepository())->findAll();
        require_once 'views/product/create.php';
    }

    public function show(){
        $repository = new ProductRepository();
        $product = $repository->find($_GET['id'] ?? null);

        if(!$product){
            showError();
            return;
        }

        $repository->attachImages([$product]);

        $GLOBALS['pageTitle'] = Utils::sanitize($product->name) . ' — Closetly';
        $description = trim(strip_tags($product->description ?? ''));
        $GLOBALS['pageDescription'] = $description !== '' ? mb_strimwidth($description, 0, 155, '…') : ('Shop ' . Utils::sanitize($product->name) . ' at Closetly.');

        require_once 'views/product/show.php';
    }

    /** Trusts the actual file bytes, not the client-supplied name/type, which are easy to spoof. */
    private function storeUploadedImage($tmpName): ?string
    {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $imageInfo = @getimagesize($tmpName);

        if(!$imageInfo || !isset($allowed[$imageInfo['mime']])) return null;

        $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$imageInfo['mime']];
        if(!is_dir('public/uploads/products/')) mkdir('public/uploads/products/', 0777, true);

        if(!move_uploaded_file($tmpName, 'public/uploads/products/'.$filename)) return null;

        return $filename;
    }

    private function canManage(?Product $product): bool
    {
        if(!$product) return false;
        if(isset($_SESSION['admin'])) return true;
        return $product->seller_id !== null && $product->seller_id == ($_SESSION['identity']['id'] ?? null);
    }
}
