<?php
class reclamation {
 



     private $reclamation_id;
     private $client_id ;
     private $type;
     private $description; 
     private $statut;
     private $date_creation;
    private $date_resolution;
   
    private $piecesJointes = []; // Tableau pour stocker les pièces jointes

  

    public function __construct($reclamation_id, $client_id,$type,$description,$status, $date_creation,$date_resolution) {
        $this->reclamation_id = $reclamation_id;
        $this->client_id = $client_id;
        $this->type = $type;
        $this->description = $description;
        $this->statut = $status;
        $this->date_creation = $date_creation;
        $this->date_resolution = $date_resolution;
    }

    public function getReclamationId() {
        return $this->reclamation_id;
    }


    // Méthode pour définir les pièces jointes
    public function setPiecesJointes($piecesJointes) {
        $this->piecesJointes = $piecesJointes;
    }

    // Méthode pour obtenir les pièces jointes
    public function getPiecesJointes() {
        return $this->piecesJointes;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }
    public function getDateResolution() {
        return $this->date_resolution;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getStatut() {
        return $this->statut;
    }
    public function getType() {
        return $this->type;
    }
    

}
