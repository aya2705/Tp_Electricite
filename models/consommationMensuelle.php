<?php
class ConsommationMensuelle
{
    private $id;
    private $kw;
    private $imagePath;
    private $createdAt;

    public function __construct($kw, $imagePath, $createdAt = null)
    {
        $this->kw = $kw;
        $this->imagePath = $imagePath;
        $this->createdAt = $createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getKw()
    {
        return $this->kw;
    }

    public function getImagePath()
    {
        return $this->imagePath;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
