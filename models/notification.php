<?php

class Notification {
    private $notification_id;
    private $client_id;
    private $type;
    private $reference;
    private $content;
    private $status;
    private $created_at;
    private $read_at;

    public function __construct($client_id, $type, $content, $reference = null, $status = 'non_lue') {
        $this->client_id = $client_id;
        $this->type = $type;
        $this->content = $content;
        $this->reference = $reference;
        $this->status = $status;
    }

    // Getters
    public function getNotificationId() { return $this->notification_id; }
    public function getClientId()       { return $this->client_id; }
    public function getType()           { return $this->type; }
    public function getReference()      { return $this->reference; }
    public function getContent()        { return $this->content; }
    public function getStatus()         { return $this->status; }
    public function getCreatedAt()      { return $this->created_at; }
    public function getReadAt()         { return $this->read_at; }

    // Setters
    public function setNotificationId($notification_id) { $this->notification_id = $notification_id; }
    public function setClientId($client_id)             { $this->client_id = $client_id; }
    public function setType($type)                      { $this->type = $type; }
    public function setReference($reference)            { $this->reference = $reference; }
    public function setContent($content)                { $this->content = $content; }
    public function setStatus($status)                  { $this->status = $status; }
    public function setCreatedAt($created_at)           { $this->created_at = $created_at; }
    public function setReadAt($read_at)                 { $this->read_at = $read_at; }
}