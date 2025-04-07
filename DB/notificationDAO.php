<?php
require_once 'connexion.php';

class NotificationDAO {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Récupérer les notifications d'un client
    public function getNotifications($clientId) {
        $query = "SELECT * FROM notifications 
                  WHERE client_id = :client_id 
                  ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ajouter une notification
    public function addNotification($notification) {
        $query = "INSERT INTO notifications (client_id, type, reference, content, status)
                  VALUES (:client_id, :type, :reference, :content, 'non_lue')";
        $stmt = $this->pdo->prepare($query);
        $params = [
            ':client_id' => $notification->getClientId(),
            ':type'      => $notification->getType(),
            ':reference' => $notification->getReference(),
            ':content'   => $notification->getContent()
        ];
        return $stmt->execute($params);
    }

    // Marquer une notification comme lue
    public function markAsRead($notificationId) {
        $query = "UPDATE notifications SET status = 'lue' WHERE notification_id = :notification_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':notification_id', $notificationId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Supprimer une notification
    public function deleteNotification($notificationId) {
        $query = "DELETE FROM notifications WHERE notification_id = :notification_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':notification_id', $notificationId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Compter les notifications non lues
    public function countUnreadNotifications($clientId) {
        $query = "SELECT COUNT(*) as count FROM notifications WHERE client_id = :client_id AND status = 'non_lue'";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['count'] : 0;
    }
}