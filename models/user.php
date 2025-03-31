<?php
class User {
    private $user_id;
    private $email;
    private $password_hash;
    private $role;
    private $created_at;

    public function __construct($user_id, $email, $password_hash, $role, $created_at) {
        $this->user_id = $user_id;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->role = $role;
        $this->created_at = $created_at;
    }

    public function getUserId() { return $this->user_id; }
    public function getEmail() { return $this->email; }
    public function getPasswordHash() { return $this->password_hash; }
    public function getRole() { return $this->role; }
    public function getCreatedAt() { return $this->created_at; }
}