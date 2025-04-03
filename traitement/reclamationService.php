<?php
 require_once '../DB/models/ReclamationDAO.php';
 session_start();
 
 // Si l'action est l'ajout de réclamation
 

 if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $client_id = $_SESSION['client_id'] ?? 1;
        $type = htmlspecialchars($_POST['claim_type']);
        $description = htmlspecialchars($_POST['description']);
        $statut = 'en_attente';

        // Ajouter la réclamation et récupérer son ID
        $reclamation_id = ReclamationDAO::addReclamation($client_id, $type, $description, $statut);

        if ($reclamation_id) {
            $_SESSION['reclamation_id'] = $reclamation_id; // Stocker en session

            // Gérer les pièces jointes
            if (!empty($_FILES['attachments']['name'][0])) {
                $uploadDir = '../ihm/Reclamation/client/uploads/';

                foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
                    $fileName = time() . "_" . basename($_FILES['attachments']['name'][$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $targetFilePath)) {
                        // Ajouter chaque fichier à la base de données
                        ReclamationDAO::addPieceJointe($reclamation_id, $fileName, $_FILES['attachments']['type'][$key]);
                    }
                }
            }

            echo "Réclamation ajoutée avec succès. ID: " . $reclamation_id;
            exit;
        } else {
            throw new Exception("Erreur lors de l'ajout de la réclamation.");
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}




 // Pour afficher les détails de la réclamation dans le modal

 $reclamationId = isset($_GET['reclamationId']) ? $_GET['reclamationId'] : null;

if ($reclamationId && filter_var($reclamationId, FILTER_VALIDATE_INT)) {
    try {
        $reclamation = ReclamationDAO::getReclamationById($reclamationId);
        if ($reclamation) {
            // Traitement de la réclamation récupérée
            $clientId = $reclamation['client_id'];
            $claimDescription = $reclamation['description'];
            $claimDate = $reclamation['date_creation'];
            $claimType = ucfirst($reclamation['type']);
            $piecesJointes = isset($reclamation['pieces_jointes']) && is_array($reclamation['pieces_jointes']) 
                ? array_map(fn($piece) => $piece['file_path'], $reclamation['pieces_jointes']) 
                : [];
            
            $client = ReclamationDAO::getClientById($clientId);

            // Vérifier si $client contient bien des données avant de l'utiliser
            if ($client && is_array($client)) {
                $clientName = htmlspecialchars($client['full_name'] ?? 'Non renseigné');
                $clientEmail = htmlspecialchars($client['email'] ?? 'Non renseigné');
                $clientPhone = htmlspecialchars($client['phone'] ?? 'Non renseigné');
                $clientAddress = htmlspecialchars($client['address'] ?? 'Non renseigné');
                $claimRef = "#REF-" . $reclamationId;

                // Construire la réponse HTML
                echo "
                    <div class='client-info'>
                        <h3>Information Client</h3>
                        <p><strong>Nom:</strong> <span>$clientName</span></p>
                        <p><strong>Email:</strong> <span>$clientEmail</span></p>
                        <p><strong>Téléphone:</strong> <span>$clientPhone</span></p>
                        <p><strong>Adresse:</strong> <span>$clientAddress</span></p>
                    </div>
                    
                    <div class='claim-detail'>
                        <h3>Détails de la réclamation</h3>
                        <p><strong>Référence:</strong> <span>$claimRef</span></p>
                        <p><strong>Type:</strong> <span>$claimType</span></p>
                        <p><strong>Date de soumission:</strong> <span>$claimDate</span></p>
                        <p><strong>Description:</strong></p>
                        <div class='claim-description'>$claimDescription</div>
                        <p><strong>Pièces jointes:</strong></p>
                        <div class='attachments'>";

                if (!empty($piecesJointes[0])) {
                    foreach ($piecesJointes as $attachment) {
                        echo "<div class='attachment'>
                                <i class='fas fa-paperclip'></i> <a href='../client/uploads/$attachment' target='_blank'>$attachment</a>
                              </div>";
                    }
                } else {
                    echo "<p>Aucune pièce jointe.</p>";
                }
                echo "</div></div>";
            } else {
                echo "<p style='color: red;'>Client introuvable.</p>";
            }
        } else {
            echo "<p style='color: red;'>Réclamation introuvable.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
    }
} else {
    echo "L'ID de réclamation est invalide ou manquant.";
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "respond") {
    $reclamation_id = isset($_POST["reclamation_id"]) ? intval($_POST["reclamation_id"]) : 0;
    $response_text = isset($_POST["response_text"]) ? trim($_POST["response_text"]) : '';
    $claim_status = isset($_POST["claim_status"]) ? $_POST["claim_status"] : 'en_traitement';

    if ($reclamation_id <= 0 || empty($response_text)) {
        die("Erreur: ID de réclamation ou texte de réponse invalide.");
    }

    if (!ReclamationDAO::traiterReponse($reclamation_id, $response_text, $claim_status)) {
        error_log("Échec du traitement de la réclamation ID: $reclamation_id, Statut: $claim_status, Réponse: $response_text");
        die("Erreur lors du traitement de la réclamation.");
    } else {
        header("Location: ../ihm/Reclamation/admin/claims-frs.php");
        exit;
    }
}

// Récupérer les réclamations lorsque la page est visitée sans POST
$client_id = $_SESSION['client_id'] ?? 1;
$reclamations = ReclamationDAO::getReclamationsByClientId($client_id);

// Stocker les réclamations dans la session pour les utiliser dans la page d'affichage
$_SESSION['reclamations'] = $reclamations;


 ?>