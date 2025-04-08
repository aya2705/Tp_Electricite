<?php
require_once __DIR__ . '/../DB/connexion.php';
require_once __DIR__ . '/../DB/notificationDAO.php';
require_once __DIR__ . '/../models/notification.php';

class NotificationService {
    private $notificationDAO;

    public function __construct() {
        $this->notificationDAO = new NotificationDAO();
    }

    // Ajouter une notification
    public function addNotification($clientId, $type, $reference, $content) {
        $notification = new Notification($clientId, $type, $content, $reference);
        return $this->notificationDAO->addNotification($notification);
    }

    // Récupérer les notifications d'un client
    public function getClientNotifications($clientId) {
        return $this->notificationDAO->getNotifications($clientId);
    }

    // Marquer une notification comme lue
    public function markAsRead($notificationId) {
        return $this->notificationDAO->markAsRead($notificationId);
    }

    // Supprimer une notification
    public function deleteNotification($notificationId) {
        return $this->notificationDAO->deleteNotification($notificationId);
    }

    // Compter les notifications non lues
    public function countUnreadNotifications($clientId) {
        return $this->notificationDAO->countUnreadNotifications($clientId);
    }
}

// Gestion de l'action POST pour suppression
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $notificationService = new NotificationService();
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['notificationId'])) {
        try {
            $notificationId = $_POST['notificationId'];
            $success = $notificationService->deleteNotification($notificationId);
            if ($success) {
                header("Location: " . $_SERVER['HTTP_REFERER'] . "?success=Notification supprimée");
            } else {
                header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Erreur lors de la suppression");
            }
            exit();
        } catch (Exception $e) {
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}
?>