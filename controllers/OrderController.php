<?php

require_once 'models/OrderRepository.php';
require_once 'models/UserRepository.php';
require_once 'helpers/Mailer.php';
require_once 'helpers/EmailTemplate.php';

class OrderController
{
    public function index()
    {
        $carshop = $_SESSION['carshop'] ?? [];
        require_once 'views/order/index.php';
    }

    public function add()
    {

        if(!isset($_SESSION['identity'])) return header('Location: /');

        $statsCarshop = Utils::statsCarShop();

        try {
            $order = new Order();
            $order->state = $_POST['state'];
            $order->city = $_POST['city'];
            $order->address = $_POST['address'];
            $order->user_id = $_SESSION['identity']['id'];
            $order->price = $statsCarshop['total'];

            $cartItems = $_SESSION['carshop'] ?? [];

            $orders = new OrderRepository();
            $orderId = $orders->insert($order);
            if (!$orderId) throw new Exception('Failed to create order');

            $orders->linkProducts($orderId, $cartItems);
            unset($_SESSION['carshop']);

            $baseUrl = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'];
            $this->notifySellers($cartItems, $orderId, $baseUrl);
            $this->notifyCustomer($cartItems, $order, $orderId, $baseUrl);

            $_SESSION['ordercreated'] = 'Order created successfully';

            header('Location: /order/done');
        } catch (Exception $e) {
            error_log('OrderController::add - ' . $e->getMessage());
            $_SESSION['orderfailed'] = 'Unable to create order';
            header('Location: /order/done');
        }
    }

    private function lineItemRow(Product $product, int $units, string $baseUrl): string
    {
        $link = EmailTemplate::productLink("{$baseUrl}/product/show&id={$product->id}", $product->name);
        return '<tr>'
            . '<td style="padding:8px 0; border-bottom:1px solid rgba(23,19,15,0.1); font-size:14px;">' . $link . '</td>'
            . '<td style="padding:8px 0; border-bottom:1px solid rgba(23,19,15,0.1); font-size:14px; text-align:center;">x' . $units . '</td>'
            . '<td style="padding:8px 0; border-bottom:1px solid rgba(23,19,15,0.1); font-size:14px; text-align:right;">$' . htmlspecialchars($product->price * $units) . ' USD</td>'
            . '</tr>';
    }

    private function notifySellers(array $cartItems, $orderId, string $baseUrl): void
    {
        $itemsBySeller = [];
        foreach ($cartItems as $item) {
            $product = $item['product'];
            if (!$product->seller_id) continue;
            $itemsBySeller[$product->seller_id][] = $this->lineItemRow($product, $item['units'], $baseUrl);
        }

        if (empty($itemsBySeller)) return;

        $users = new UserRepository();
        foreach ($itemsBySeller as $sellerId => $rows) {
            $seller = $users->find($sellerId);
            if (!$seller) continue;

            $body = '<h2 style="font-size:20px; margin:0 0 12px;">New order!</h2>'
                . '<p style="font-size:14px; line-height:1.6; margin:0 0 20px;">Hi ' . htmlspecialchars($seller->name) . ', someone just ordered your product(s) in order <strong>#' . $orderId . '</strong>:</p>'
                . '<table style="width:100%; border-collapse:collapse; margin-bottom:24px;">' . implode('', $rows) . '</table>'
                . EmailTemplate::button("{$baseUrl}/order/show&id={$orderId}", 'View order');

            Mailer::send($seller->email, 'New order on Closetly', EmailTemplate::wrap($body));
        }
    }

    private function notifyCustomer(array $cartItems, Order $order, $orderId, string $baseUrl): void
    {
        $identity = $_SESSION['identity'] ?? null;
        if (!$identity || empty($identity['email'])) return;

        $rows = '';
        foreach ($cartItems as $item) {
            $rows .= $this->lineItemRow($item['product'], $item['units'], $baseUrl);
        }

        $body = '<h2 style="font-size:20px; margin:0 0 12px;">Order confirmed!</h2>'
            . '<p style="font-size:14px; line-height:1.6; margin:0 0 20px;">Thanks for your order, ' . htmlspecialchars($identity['name']) . '. Here is your summary for order <strong>#' . $orderId . '</strong>:</p>'
            . '<table style="width:100%; border-collapse:collapse; margin-bottom:12px;">' . $rows . '</table>'
            . '<p style="font-size:14px; text-align:right; margin:0 0 20px;"><strong>Total: $' . htmlspecialchars($order->price) . ' USD</strong></p>'
            . '<p style="font-size:13px; line-height:1.6; margin:0 0 20px; color:rgba(23,19,15,0.7);">Shipping to: ' . htmlspecialchars($order->address) . ', ' . htmlspecialchars($order->city) . ', ' . htmlspecialchars($order->state) . '</p>'
            . EmailTemplate::button("{$baseUrl}/order/show&id={$orderId}", 'View your order');

        Mailer::send($identity['email'], 'Your Closetly order is confirmed', EmailTemplate::wrap($body));
    }

    public function done()
    {
        if (!isset($_SESSION['identity'])) return header('Location: /views/order/index.php');
        $identity = $_SESSION['identity'];

        $orders = new OrderRepository();
        $order = isset($_SESSION['ordercreated']) ? $orders->findOneByUser($identity['id']) : null;
        $products = $order ? $orders->findProductsByOrder($order->id) : [];

        if (isset($_SESSION['orderfailed'])) $GLOBALS['pageTitle'] = 'Order Failed — Closetly';

        require_once 'views/order/done.php';
        Utils::deleteSession('ordercreated');
        Utils::deleteSession('orderfailed');
    }

    public function myOrders(){
        Utils::isIdentity();
        $repository = new OrderRepository();
        $userId = $_SESSION['identity']['id'];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $orders = $repository->findAllByUser($userId, $page, $perPage);
        $totalPages = max(1, (int) ceil($repository->countByUser($userId) / $perPage));
        $paginationBase = '/order/myorders';
        require_once 'views/order/myorders.php';
    }

    public function show(){
        Utils::isIdentity();

        if(!isset($_GET['id'])) return header('Location: /order/myorders');

        $orders = new OrderRepository();
        $order = $orders->find($_GET['id']);

        if(!$order || (!isset($_SESSION['admin']) && $order->user_id != $_SESSION['identity']['id'])){
            return header('Location: /order/myorders');
        }

        $products = $orders->findProductsByOrder($_GET['id']);
        require_once 'views/order/show.php';
    }

    public function manage(){
        Utils::isAdmin();
        $manage = true;
        $repository = new OrderRepository();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $orders = $repository->findAll($page, $perPage);
        $totalPages = max(1, (int) ceil($repository->countAll() / $perPage));
        $paginationBase = '/order/manage';
        require_once 'views/order/myorders.php';
    }

    public function status(){
        Utils::isAdmin();
        if(!isset($_POST['id']) || !isset($_POST['status'])) return header('Location: /order/manage');
        (new OrderRepository())->updateStatus($_POST['id'], Utils::sanitize($_POST['status']));
        header('Location: /order/show&id='.$_POST['id']);
    }


}
