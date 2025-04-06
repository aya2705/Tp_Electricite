<?php


class reclamation{
   private $reclamation_id;
    private $client_id;
    private $type ;
    private $description;
    private $statut ;
    private $date_creation ;
    private $date_resolution ;
  

    public function __construct($reclamation_id, $client_id, $type, $description, $statut, $date_creation = null, $date_resolution = null){
        $this->reclamation_id = $reclamation_id;
        $this->client_id = $client_id;
        $this->type = $type;
        $this->description = $description;
        $this->statut = $statut;
        $this->date_creation = $date_creation;
        $this->date_resolution = $date_resolution;
    }

    public function getReclamationId(){return $this->reclamation_id;}
    public function getClientId(){return $this->client_id;}
    public function getType(){ return $this->type;}
    public function getDescription() {return $this->description;}
    public function getStatut(){return $this->statut;}
    public function getDateCreation(){ return $this->date_creation;}
    public function getDateResolution(){return $this->date_resolution;}

    public function setReclamationId($reclamation_id){$this->reclamation_id = $reclamation_id;}
    public function setClientId($client_id){$this->client_id = $client_id;}
    public function setType($type){$this->type = $type;}
    public function setDescription($description){$this->description = $description;}
    public function setStatut($statut){$this->statut = $statut;}
    public function setDateCreation($date_creation){$this->date_creation = $date_creation;}
    public function setDateResolution($date_resolution){$this->date_resolution = $date_resolution;}
    
}