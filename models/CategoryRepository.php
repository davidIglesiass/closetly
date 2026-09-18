<?php

require_once 'models/Category.php';

class CategoryRepository
{
    private $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? MyPDO::connect();
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT c.*, d.name as departmentName FROM categories c LEFT JOIN categories d ON d.id = c.parent_id ORDER BY COALESCE(c.parent_id, c.id), c.parent_id IS NULL DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Category::class);
    }

    public function findDepartments(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Category::class);
    }

    public function find($id): ?Category
    {
        $stmt = $this->db->prepare("SELECT c.*, d.name as departmentName FROM categories c LEFT JOIN categories d ON d.id = c.parent_id WHERE c.id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $category = $stmt->fetchObject(Category::class);
        return $category ?: null;
    }

    public function insert(Category $category): bool
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name, parent_id) VALUES(:name, :parent_id)");
        $stmt->bindParam(':name', $category->name);
        $stmt->bindParam(':parent_id', $category->parent_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al guardar la categoria: ' . $e->getMessage());
            return false;
        }
    }

    /** Fails (returns false) if the category still has subcategories or products - the FK constraint catches it. */
    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al borrar la categoria: ' . $e->getMessage());
            return false;
        }
    }
}
