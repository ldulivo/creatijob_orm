<?php

namespace Src\Models;

use App\Db\Model;

class UserModel extends Model
{
    protected $table = 'users';

    /**
     * Busca un usuario por su nombre de usuario (username)
     */
    public static function findByUsername(string $username): ?array
    {
        try {
            $instance = new self();
            $user = $instance->fetch(
                "SELECT * FROM users WHERE username = ? LIMIT 1",
                [$username]
            );
            return $user ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Busca un usuario por su ID
     */
    public static function findById(int $id): ?array
    {
        try {
            $instance = new self();
            $user = $instance->fetch(
                "SELECT * FROM users WHERE id = ? LIMIT 1",
                [$id]
            );
            return $user ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Verifica credenciales (username y password)
     */
    public static function verifyCredentials(string $username, string $password): bool
    {
        $user = self::findByUsername($username);
        if (!$user || empty($user['password'])) {
            return false;
        }
        return password_verify($password, $user['password']);
    }

    /**
     * Compara la contraseña de un usuario
     */
    public static function comparePassword(string $username, string $password): bool
    {
        return self::verifyCredentials($username, $password);
    }

    /**
     * Obtiene el rol de un usuario
     */
    public static function getRole(string $username): string
    {
        $user = self::findByUsername($username);
        return $user['role'] ?? 'admin';
    }

    /**
     * Obtiene el estado de un usuario ('active' o 'inactive')
     */
    public static function getStatus(string $username): string
    {
        $user = self::findByUsername($username);
        return $user['status'] ?? 'active';
    }

    /**
     * Crea un nuevo usuario con contraseña hasheada
     */
    public static function createUser(array $data): bool
    {
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $name = trim($data['name'] ?? $username);
        $role = trim($data['role'] ?? 'admin');
        $status = trim($data['status'] ?? 'active');
        $plainPassword = $data['password'] ?? '';

        $allowedRoles = ['admin', 'owner', 'employee', 'user'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'admin';
        }

        $allowedStatuses = ['active', 'inactive'];
        if (!in_array($status, $allowedStatuses)) {
            $status = 'active';
        }

        if (empty($username) || empty($plainPassword)) {
            return false;
        }

        if (self::findByUsername($username)) {
            return false;
        }

        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        $instance = new self();

        $sql = "INSERT INTO users (name, username, email, role, status, password, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

        try {
            $instance->execute($sql, [$name, $username, $email, $role, $status, $hashedPassword]);
            return true;
        } catch (\Throwable $e) {
            // Fallback en caso de que columnas opcionales no existan aún
            try {
                $fallbackSql = "INSERT INTO users (name, username, email, role, password, created_at, updated_at)
                                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
                $instance->execute($fallbackSql, [$name, $username, $email, $role, $hashedPassword]);
                return true;
            } catch (\Throwable $e2) {
                return false;
            }
        }
    }

    /**
     * Actualiza un usuario existente
     */
    public static function updateUser(string $username, array $data): bool
    {
        $user = self::findByUsername($username);
        if (!$user) {
            return false;
        }

        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = "`name` = ?";
            $params[] = trim($data['name']);
        }
        if (isset($data['email'])) {
            $fields[] = "`email` = ?";
            $params[] = trim($data['email']);
        }
        if (isset($data['role'])) {
            $role = trim($data['role']);
            $allowedRoles = ['admin', 'owner', 'employee', 'user'];
            if (in_array($role, $allowedRoles)) {
                $fields[] = "`role` = ?";
                $params[] = $role;
            }
        }
        if (isset($data['status'])) {
            $status = trim($data['status']);
            if (in_array($status, ['active', 'inactive'])) {
                $fields[] = "`status` = ?";
                $params[] = $status;
            }
        }
        if (!empty($data['password'])) {
            $fields[] = "`password` = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return true;
        }

        $fields[] = "`updated_at` = NOW()";
        $params[] = $username;

        try {
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE username = ?";
            $instance = new self();
            $instance->execute($sql, $params);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Actualiza la contraseña de un usuario
     */
    public static function updatePassword(string $username, string $newPassword): bool
    {
        return self::updateUser($username, ['password' => $newPassword]);
    }

    /**
     * Actualiza el estado de un usuario (active / inactive)
     */
    public static function setStatus(string $username, string $status): bool
    {
        return self::updateUser($username, ['status' => $status]);
    }

    /**
     * Elimina un usuario por su username
     */
    public static function deleteUser(string $username): bool
    {
        $user = self::findByUsername($username);
        if (!$user) {
            return false;
        }

        try {
            $instance = new self();
            $instance->execute("DELETE FROM users WHERE username = ?", [$username]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios
     */
    public static function getAll(): array
    {
        try {
            $instance = new self();
            $users = $instance->fetchAll("SELECT * FROM users ORDER BY id ASC") ?: [];
            return array_map(function ($u) {
                unset($u['password']);
                if (!isset($u['status'])) {
                    $u['status'] = 'active';
                }
                return $u;
            }, $users);
        } catch (\Throwable $e) {
            return [];
        }
    }
}

