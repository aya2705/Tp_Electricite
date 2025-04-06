<?php

require_once 'connexion.php';  // Assurez-vous d'inclure la classe Database
 
 class NotificationDAO {
     private $pdo;
 
     public function __construct() {
         // Utilisez le singleton pour obtenir l'instance de la connexion à la base de données
         $this->pdo = Database::getInstance()->getConnection();
     }
 
   
 
     public function getNotifications($userId) {
         // Sélectionner les notifications non lues liées aux réclamations du client
         $query = "SELECT * FROM notifications WHERE reclamation_id IN 
                   (SELECT reclamation_id FROM reclamations WHERE client_id = :user_id) 
                   ORDER BY date_reponse DESC";
         
         $stmt = $this->pdo->prepare($query);
         $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
         $stmt->execute();
         
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }
     
 
    
 
     public function deleteNotification($notificationId) {
         $query = "DELETE FROM notifications WHERE reponse_id = :notification_id";
         $stmt = $this->pdo->prepare($query);
         $stmt->bindParam(':notification_id', $notificationId, PDO::PARAM_INT);
         return $stmt->execute();
     }
 }