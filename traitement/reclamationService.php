<?php
require_once '../DB/models/Reclamation.php';
session_start();

// Si l'action est l'ajout de réclamation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    try {
        $client_id = $_SESSION['client_id'] ?? 1;
        $type = htmlspecialchars($_POST['claim_type']);
        $description = htmlspecialchars($_POST['description']);
        $statut = 'en_attente';
        $pieces_jointes = null;

        // Vérification et téléchargement des pièces jointes
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

// Pour afficher les détails de la réclamation dans le modal

// Code principal pour récupérer les informations
$reclamationId = $_GET['reclamation_id'] ?? null;

if ($reclamationId && is_numeric($reclamationId)) {
    try {
        // Récupérer la réclamation depuis la base de données
        $reclamation = Reclamation::getReclamationById($reclamationId);

        if ($reclamation) {
            // Vérification et récupération des informations
            $clientId = $reclamation['client_id']; 
            $claimDescription = $reclamation['description'];  // Description
            $claimDate = $reclamation['date_creation']; 
            $piecesJointes = isset($reclamation['pieces_jointes']) ? explode(',', $reclamation['pieces_jointes']) : [];  // Si 'pieces_jointes' existe, le séparer en tableau

            // Si la description est vide ou non définie, utilisez une valeur par défaut
            if (empty($claimDescription)) {
                $claimDescription = "Aucune description disponible.";
            }

            // Récupérer les détails du client
            $client = Reclamation::getClientById($clientId);

            if ($client) {
                $clientName = $client['full_name'];
                $clientAddress = $client['address'];
                $clientPhone = $client['phone'];
                $claimRef = "#REF-" . $reclamationId;

                // Passer les informations au modal HTML
                include '../ihm/Reclamation/claims-frs.php'; // Afficher le modal
            } else {
                echo "Client introuvable.";
            }
        } else {
            echo "Réclamation introuvable.";
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "L'ID de la réclamation est manquant ou invalide!";
}
?>
