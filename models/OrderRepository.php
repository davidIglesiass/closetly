<?php

require_once 'models/Order.php';
require_once 'models/Product.php';

class OrderRepository
{
    private $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? MyPDO::connect();
    }

    public function findAll(int $page = 1, int $perPage = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Order::class);
    }

    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    public function find($id): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $order = $stmt->fetchObject(Order::class);
        return $order ?: null;
    }

    public function findOneByUser($userId): ?Order
    {
        $stmt = $this->db->prepare("SELECT o.id, o.price FROM orders o WHERE o.user_id = :user_id ORDER BY o.id DESC LIMIT 1");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $order = $stmt->fetchObject(Order::class);
        return $order ?: null;
    }

    public function findAllByUser($userId, int $page = 1, int $perPage = 10): array
    {
        $stmt = $this->db->prepare("SELECT o.* FROM orders o WHERE o.user_id = :user_id ORDER BY o.id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Order::class);
    }

    public function countByUser($userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function findProductsByOrder($orderId): array
    {
        $stmt = $this->db->prepare("SELECT p.*, pho.quantity, c.name as categoryName FROM products p INNER JOIN products_has_orders pho ON p.id = pho.product_id LEFT JOIN categories c ON c.id = p.category_id WHERE pho.order_id = :order_id");
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Product::class);
    }

    public function insert(Order $order)
    {
        $state = Utils::sanitize($order->state);
        $city = Utils::sanitize($order->city);
        $address = Utils::sanitize($order->address);

        $stmt = $this->db->prepare("INSERT INTO orders (user_id, state, city, address, price, status, created_at) VALUES(:user_id, :state, :city, :address, :price, 'requested', CURRENT_TIMESTAMP)");

        $stmt->bindParam(':user_id', $order->user_id);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':price', $order->price);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al guardar la orden: ' . $e->getMessage());
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    public function linkProducts(int $orderId, array $cartItems): void
    {
        $stmt = $this->db->prepare("INSERT INTO products_has_orders (order_id, product_id, quantity) VALUES(:order_id, :product_id, :quantity)");

        foreach ($cartItems as $item) {
            $productId = $item['product']->id;
            $quantity = $item['units'];
            $stmt->bindParam(':order_id', $orderId);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->execute();
        }
    }

    public function updateStatus($id, $status): void
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
