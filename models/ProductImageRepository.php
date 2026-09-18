<?php

/** Extra gallery images beyond a product's cover (products.image) - one row per file. */
class ProductImageRepository
{
    private $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? MyPDO::connect();
    }

    public function insert(int $productId, string $image): bool
    {
        $stmt = $this->db->prepare("INSERT INTO product_images (product_id, image) VALUES (:product_id, :image)");
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al guardar la imagen del producto: ' . $e->getMessage());
            return false;
        }
    }
}
