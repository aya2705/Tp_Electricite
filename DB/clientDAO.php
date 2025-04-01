<?php
require_once __DIR__ . '/../models/Client.php';

class ClientDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($client_id) {
        $stmt = $this->conn->prepare("
            SELECT c.*, u.email 
            FROM clients c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.client_id = ?
        ");
        $stmt->execute([$client_id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Client($data);
    }

 
    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("
            SELECT c.*, u.email 
            FROM clients c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Client($data);
    }

    public function update(Client $client) {
        $stmt = $this->conn->prepare("
            UPDATE clients SET
            full_name = ?,
            address = ?,
            phone = ?
            WHERE client_id = ?
        ");

        return $stmt->execute([
            $client->getFullName(),
            $client->getAddress(),
            $client->getPhone(),
            $client->getClientId()
        ]);
    }
}