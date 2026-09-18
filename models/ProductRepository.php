<?php

require_once 'models/Product.php';

class ProductRepository
{
    private $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? MyPDO::connect();
    }

    public function findAll(int $page = 1, int $perPage = 10): array
    {
        $stmt = $this->db->prepare("SELECT p.*, u.name as sellerName FROM products p LEFT JOIN users u ON u.id = p.seller_id ORDER BY p.id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Product::class);
    }

    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function findAllBySeller($sellerId, int $page = 1, int $perPage = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE seller_id = :seller_id ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':seller_id', $sellerId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Product::class);
    }

    public function countBySeller($sellerId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE seller_id = :seller_id");
        $stmt->bindParam(':seller_id', $sellerId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function findAllByCategory($categoryId): array
    {
        $stmt = $this->db->prepare("SELECT p.*, c.name as categoryName FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE p.category_id = :category_id ORDER BY id DESC");
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Product::class);
    }

    public function find($id): ?Product
    {
        $stmt = $this->db->prepare("SELECT p.*, c.name as categoryName FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetchObject(Product::class);
        return $product ?: null;
    }

    public function findRandom($count): array
    {
        $stmt = $this->db->prepare("SELECT p.*, c.name as categoryName FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY RAND() LIMIT :rand");
        $stmt->bindValue(':rand', (int) $count, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Product::class);
    }

    public function insert(Product $product)
    {
        $stmt = $this->db->prepare("INSERT INTO products VALUES(null, :category_id, :seller_id, :name, :description, :price, :discount, :stock, :image, CURRENT_TIMESTAMP)");

        $stmt->bindParam(':category_id', $product->category_id);
        $stmt->bindParam(':seller_id', $product->seller_id);
        $stmt->bindParam(':name', $product->name);
        $stmt->bindParam(':description', $product->description);
        $stmt->bindParam(':price', $product->price);
        $stmt->bindParam(':discount', $product->discount);
        $stmt->bindParam(':stock', $product->stock);
        $stmt->bindParam(':image', $product->image);

        try {
            return $stmt->execute() ? (int) $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log('Error al guardar el producto: ' . $e->getMessage());
            return false;
        }
    }

    public function attachImages(array $products): array
    {
        if (empty($products)) return $products;

        $ids = array_map(fn($p) => $p->id, $products);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT product_id, image FROM product_images WHERE product_id IN ($placeholders) ORDER BY id ASC");
        $stmt->execute($ids);

        $extrasByProduct = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $extrasByProduct[$row['product_id']][] = $row['image'];
        }

        foreach ($products as $product) {
            $extras = $extrasByProduct[$product->id] ?? [];
            $product->images = $product->image ? array_merge([$product->image], $extras) : $extras;
        }

        return $products;
    }

    public function update(Product $product): bool
    {
        $sql = "UPDATE products SET category_id = :category_id, name = :name, description = :description, price = :price, discount = :discount, stock = :stock";

        if ($product->image !== null) $sql .= ", image = :image";

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':category_id', $product->category_id);
        $stmt->bindParam(':name', $product->name);
        $stmt->bindParam(':description', $product->description);
        $stmt->bindParam(':price', $product->price);
        $stmt->bindParam(':discount', $product->discount);
        $stmt->bindParam(':stock', $product->stock);
        if ($product->image !== null) $stmt->bindParam(':image', $product->image);
        $stmt->bindParam(':id', $product->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al actualizar el producto: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al borrar el producto: ' . $e->getMessage());
            return false;
        }
    }
}
