<?php
class ConsommationMensuelle {
    public $id;
    public $kw;
    private $imagePath; 
    private $createdAt; // important to check last submited consumtion
    private $isAbnormal;
    
    public function __construct($kw, $imagePath, $createdAt = null, $isAbnormal ) {
        $this->isAbnormal = $isAbnormal;
        $this->kw = $kw;
        $this->imagePath = $imagePath;
        $this->createdAt = $createdAt ?: new DateTime();
    }
    
     public function getId() { return $this->id; }
     public function getKw() { return $this->kw; }
     public function getImagePath() { return $this->imagePath; }
     public function getCreatedAt() { return $this->createdAt; }
     public function getIsAbnormal() { return $this->isAbnormal; }
 
     public function setId($id) { $this->id = $id; }
     public function setKw($kw) { $this->kw = $kw; }
     public function setImagePath($imagePath) { $this->imagePath = $imagePath; }
     public function setIsAbnormal($isAbnormal) { $this->isAbnormal = $isAbnormal; }

}
