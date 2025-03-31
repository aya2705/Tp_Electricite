<?php
require_once '../DB/models/Reclamation.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    try {
        $client_id = $_SESSION['client_id'] ?? 1;
        $type = htmlspecialchars($_POST['claim_type']);
        $description = htmlspecialchars($_POST['description']);
        $statut = 'en_attente';
        $pieces_jointes = null;

        if (!empty($_FILES['attachments']['name'][0])) {
            $uploadDir = '../ihm/Reclamation/uploads/';
            $uploadedFiles = [];

            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
                $fileName = time() . "_" . basename($_FILES['attachments']['name'][$key]);
                $targetFilePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    $uploadedFiles[] = $fileName;
                }
            }

            $pieces_jointes = implode(',', $uploadedFiles);
        }

        if (Reclamation::addReclamation($client_id, $type, $description, $statut, $pieces_jointes)) {
            header("Location: ../ihm/Reclamation/claims.php");
            exit;
        } else {
            echo "Erreur lors de l'ajout de la réclamation.";
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
