<?php

require_once 'models/User.php';

class UserRepository
{
    private $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? MyPDO::connect();
    }

    public function insert(User $user): bool
    {
        $stmt = $this->db->prepare("INSERT INTO users VALUES(null, :name, :email, :password, :rol, null, CURRENT_TIMESTAMP)");
        $hashedPassword = password_hash($user->password, PASSWORD_DEFAULT);
        $rol = $user->rol ?? 'user';

        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':rol', $rol);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al guardar el usuario: ' . $e->getMessage());
            return false;
        }
    }

    /** Never the password hash - never hand that to a view. */
    public function find($id): ?User
    {
        $stmt = $this->db->prepare("SELECT id, name, email, rol, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetchObject(User::class);
        return $user ?: null;
    }

    /** Every user except their password hash - never hand that to a view. */
    public function findAll(int $page = 1, int $perPage = 10): array
    {
        $stmt = $this->db->prepare("SELECT id, name, email, rol, created_at FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function update(User $user, bool $updatePassword = false): bool
    {
        if ($updatePassword) {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email, password = :password, rol = :rol WHERE id = :id");
            $hashedPassword = password_hash($user->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email, rol = :rol WHERE id = :id");
        }

        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':rol', $user->rol);
        $stmt->bindParam(':id', $user->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al actualizar el usuario: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Error al borrar el usuario: ' . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}
