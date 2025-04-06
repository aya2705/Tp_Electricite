<?php
require_once __DIR__ . '/../DB/connexion.php';
require_once  __DIR__ . "/../DB/notificationDAO.php";

$notificationDAO = new NotificationDAO();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['notificationId'])) {
        try {
            $notificationId = $_POST['notificationId'];
            $success = $notificationDAO->deleteNotification($notificationId);
            
            if ($success) {
                header("Location: ".$_SERVER['HTTP_REFERER']."?success=Notification supprimée");
            } else {
                header("Location: ".$_SERVER['HTTP_REFERER']."?error=Erreur lors de la suppression");
            }
            exit();
        } catch (Exception $e) {
            header("Location: ".$_SERVER['HTTP_REFERER']."?error=".urlencode($e->getMessage()));
            exit();
        }
    }
}
?>