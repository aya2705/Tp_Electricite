<?php
require_once 'connexion.php';
require_once '../models/user.php';

class UserDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? new User($row['user_id'], $row['email'], $row['password_hash'], $row['role'], $row['created_at']) : null;
    }

    public function createUser($email, $password, $role = 'client') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (email, password_hash, role) VALUES (:email, :password, :role)");
        $stmt->execute(['email' => $email, 'password' => $hashedPassword, 'role' => $role]);
        return $this->db->lastInsertId();
    }
}