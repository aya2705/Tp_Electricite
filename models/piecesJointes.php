<?php
class piecesJointes {
 


        private $piece_id; 
        private $reclamation_id;
        private $file_path;
        private $type;
    
        public function __construct($piece_id, $reclamation_id, $file_path, $type) {
            $this->piece_id = $piece_id;
            $this->reclamation_id = $reclamation_id;
            $this->file_path = $file_path;
            $this->type = $type;
        }
    
        public function getPieceId() {
            return $this->piece_id;
        }
    
        public function getReclamationId() {
            return $this->reclamation_id;
        }
    
        public function getFilePath() {
            return $this->file_path;
        }
    
        public function getType() {
            return $this->type;
        }
    }
    

