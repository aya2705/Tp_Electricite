<?php
class Client {
    private $client_id;
    private $user_id;
    private $full_name;
    private $address;
    private $phone;
    private $email;

    public function __construct($data) {
        $this->client_id = $data['client_id'];
        $this->user_id = $data['user_id'];
        $this->full_name = $data['full_name'];
        $this->address = $data['address'];
        $this->phone = $data['phone'] ?? null;
        $this->email = $data['email'] ?? null;
    }


    public function getClientId() { return $this->client_id; }
    public function getUserId() { return $this->user_id; }
    public function getFullName() { return $this->full_name; }
    public function getAddress() { return $this->address; }
    public function getPhone() { return $this->phone; }
    public function getEmail() { return $this->email; }

    
    public function setFullName($name) { $this->full_name = $name; }
    public function setAddress($address) { $this->address = $address; }
    public function setPhone($phone) { $this->phone = $phone; }
}