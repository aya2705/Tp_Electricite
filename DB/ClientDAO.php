<?php
require_once 'connexion.php';
require_once __DIR__ . '/../models/Client.php';

class ClientDAO
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getClientById($client_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE client_id = :client_id AND user_id IS NULL");
        $stmt->execute(['client_id' => $client_id]);
        $row = $stmt->fetch();
        return $row ? new Client($row['client_id'], $row['user_id'], $row['full_name'], $row['address'], $row['phone'], $row['created_at']) : null;
    }

    public function getClientByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $row = $stmt->fetch();
        return $row ? new Client($row['client_id'], $row['user_id'], $row['full_name'], $row['address'], $row['phone'], $row['created_at']) : null;
    }

    public function linkUserToClient($client_id, $user_id)
    {
        $stmt = $this->db->prepare("UPDATE clients SET user_id = :user_id WHERE client_id = :client_id");
        return $stmt->execute(['user_id' => $user_id, 'client_id' => $client_id]);
    }
}