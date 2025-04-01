<?php
require_once dirname(__DIR__) . "/db.php";
 
 class Reclamation {
 
     private $db;
 
     // Constructeur pour la connexion à la base de données
     public function __construct($db) {
         $this->db = $db;
     }
 
     public static function getReclamationsByClientId($client_id = 1) { // client_id par défaut à 1
         $conn = DB::getConnection();
         $sql = "SELECT * FROM reclamations WHERE client_id = ? ORDER BY date_creation DESC";
         $stmt = $conn->prepare($sql);
         $stmt->execute([$client_id]);
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }
 
     public static function addReclamation($client_id = 1, $type, $description, $statut, $pieces_jointes) { // client_id par défaut à 1
         $conn = DB::getConnection();
         $sql = "INSERT INTO reclamations (client_id, type, description, statut, pieces_jointes) 
                 VALUES (?, ?, ?, ?, ?)";
         $stmt = $conn->prepare($sql);
         return $stmt->execute([$client_id, $type, $description, $statut, $pieces_jointes]);
     }
 
     public static function getAllReclamations() {
         $conn = DB::getConnection();
         $sql = "SELECT * FROM reclamations ORDER BY date_creation DESC";
         $stmt = $conn->prepare($sql);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }
 
     // Méthode pour récupérer un client par son ID
         public static function getClientById($clientId = 1) { // clientId par défaut à 1
             $conn = DB::getConnection(); // Récupérer la connexion à la base de données
             $query = "SELECT c.*, u.email 
             FROM clients c
             JOIN users u ON c.client_id = u.user_id 
             WHERE c.client_id = :clientId";             $stmt = $conn->prepare($query);
             $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
             $stmt->execute();
 
             return $stmt->fetch(PDO::FETCH_ASSOC);
         }
 
         // Méthode pour récupérer une réclamation par son ID
         public static function getReclamationById($reclamationId) {
             $conn = DB::getConnection(); // Récupérer la connexion à la base de données
             $query = "SELECT * FROM reclamations WHERE reclamation_id = :reclamationId";  // Utiliser 'reclamation_id'
             $stmt = $conn->prepare($query);
             $stmt->bindParam(':reclamationId', $reclamationId, PDO::PARAM_INT);
             $stmt->execute();
 
             return $stmt->fetch(PDO::FETCH_ASSOC);
         }

         public static function updateStatus($id, $status) {
            $db = db::getConnection();
            $stmt = $db->prepare("UPDATE reclamations SET statut = ? WHERE reclamation_id = ?");
            return $stmt->execute([$status, $id]);
        }
        
        
 
 }

 ?>