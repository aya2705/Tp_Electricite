<?php
class Client
{
    private $client_id;
    private $user_id;
    private $full_name;
    private $address;
    private $phone;
    private $created_at;

    public function __construct($client_id, $user_id, $full_name, $address, $phone, $created_at = null)
    {
        $this->client_id = $client_id;
        $this->user_id = $user_id;
        $this->full_name = $full_name;
        $this->address = $address;
        $this->phone = $phone;
        $this->created_at = $created_at;
    }

    public function getClientId()
    {
        return $this->client_id;
    }
    public function getUserId()
    {
        return $this->user_id;
    }
    public function getFullName()
    {
        return $this->full_name;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}