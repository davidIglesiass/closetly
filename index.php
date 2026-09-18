<?php

// Loaded before session_start() so PHP can unserialize a Product already sitting in $_SESSION['carshop'].
require_once 'models/Product.php';

session_start();
require_once 'helpers/utils.php';
Utils::generateCsrfToken();
// Buffers output so a controller's header('Location: ...') redirect still works after header.php/sidebar.php print HTML.
ob_start();
require_once 'autoload.php';
require_once 'helpers/utils.php';
require_once 'config/pdo.php';
require_once 'config/constants.php';

$pageTitles = [
    'ProductController/index' => 'Closetly — Streetwear & Graphic Tees',
    'ProductController/manage' => 'Manage Products — Closetly',
    'ProductController/create' => 'Add Product — Closetly',
    'ProductController/update' => 'Edit Product — Closetly',
    'CategoryController/index' => 'Manage Categories — Closetly',
    'OrderController/index' => 'Checkout — Closetly',
    'OrderController/myorders' => 'My Orders — Closetly',
    'OrderController/manage' => 'Manage Orders — Closetly',
    'OrderController/show' => 'Order Details — Closetly',
    'OrderController/done' => 'Order Confirmed — Closetly',
    'UserController/create' => 'Create Account — Closetly',
    'UserController/manage' => 'Manage Users — Closetly',
    'UserController/adminCreate' => 'Create User — Closetly',
    'UserController/edit' => 'Edit User — Closetly',
    'CarshopController/index' => 'Your Cart — Closetly',
];
$pageTitle = 'Closetly — Streetwear & Graphic Tees';
$pageDescription = 'Closetly — graphic tees and hoodies for everyday wear. Shop the current drop.';

function showError(){
    http_response_code(404);
    $error = new ErrorController();
    $error->index();
}

if(isset($_GET['controller'])){
    $controllerName = ucfirst($_GET['controller']).'Controller';
}elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {
    $controllerName = controller_default;
}else{
    showError();
    exit();
}

// Dispatch first, buffered, so a controller can resolve its real subject before header.php prints <title>.
ob_start();
if(class_exists($controllerName)){
    $controller = new $controllerName();
    $action = null;
    if(isset($_GET['action']) && method_exists($controller, $_GET['action'])){
        $action = $_GET['action'];
    } elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {
        $action = action_default;
    }
    if($action){
        $pageTitle = $pageTitles[$controllerName.'/'.$action] ?? $pageTitle;
        $controller->$action();
    }else{
        showError();
    }
}else{
    showError();
}
$mainContent = ob_get_clean();

require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';
echo $mainContent;
require_once 'views/layout/footer.php';