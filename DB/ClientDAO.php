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
        return $row ? new Client(
            $row['client_id'], 
            $row['user_id'], 
            $row['full_name'], 
            $row['address'], 
            $row['phone'], 
            $row['created_at']
        ) : null;
    }

    public function getClientByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $row = $stmt->fetch();
        return $row ? new Client(
            $row['client_id'], 
            $row['user_id'], 
            $row['full_name'], 
            $row['address'], 
            $row['phone'], 
            $row['created_at']
        ) : null;
    }

    public function createClient($full_name, $phone, $address)
    {
        $stmt = $this->db->prepare("INSERT INTO clients (full_name, phone, address, created_at) VALUES (:full_name, :phone, :address, NOW())");
        $stmt->execute([
            'full_name' => $full_name,
            'phone'     => $phone,
            'address'   => $address
        ]);
        return $this->db->lastInsertId();
    }

    public function linkUserToClient($client_id, $user_id)
    {
        $stmt = $this->db->prepare("UPDATE clients SET user_id = :user_id WHERE client_id = :client_id");
        return $stmt->execute(['user_id' => $user_id, 'client_id' => $client_id]);
    }

    public function getAllClients()
    {
        $stmt = $this->db->prepare("SELECT * FROM clients");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteClient($client_id)
    {
        $stmt = $this->db->prepare("DELETE FROM clients WHERE client_id = :client_id");
        return $stmt->execute(['client_id' => $client_id]);
    }

    public function updateClient($client_id, $full_name, $phone, $address)
    {
        $stmt = $this->db->prepare("UPDATE clients SET full_name = :full_name, phone = :phone, address = :address WHERE client_id = :client_id");
        return $stmt->execute([
            'full_name' => $full_name,
            'phone'     => $phone,
            'address'   => $address,
            'client_id' => $client_id
        ]);
    }
}