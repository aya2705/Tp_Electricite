<?php
require_once '../DB/ReclamationDAO.php';
session_start();

// Si l'action est l'ajout de réclamation

class ReclamationService {

    public function ajouterReclamation($client_id, $type, $description, $fichiers) {
        try {
            $statut = 'en_attente';

            // Ajouter la réclamation et récupérer son ID
            $reclamationDAO = new ReclamationDAO();
            $reclamation_id = $reclamationDAO->addReclamation($client_id, $type, $description, $statut);

            if (!$reclamation_id) {
                throw new Exception("Erreur lors de l'ajout de la réclamation.");
            }

            $_SESSION['reclamation_id'] = $reclamation_id; // Stocker en session

            // Gérer les pièces jointes si présentes
            if (!empty($fichiers['name'][0])) {
                $this->ajouterPiecesJointes($reclamation_id, $fichiers);
            }

            // Rediriger après l'ajout
            header("Location: /ihm/client/claims.php?success=claimAdded");
            exit;
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    private function ajouterPiecesJointes($reclamation_id, $fichiers) {
        $uploadDir = __DIR__ . '../../uploads/claims/';

        // Vérifier si le dossier existe, sinon le créer
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Crée le dossier si nécessaire
        }

        foreach ($fichiers['tmp_name'] as $key => $tmpName) {
            $fileName = time() . "_" . basename($fichiers['name'][$key]);
            $targetFilePath = $uploadDir . $fileName;

            // Vérifier la taille du fichier (par exemple, maximum 2 Mo)
            if ($fichiers['size'][$key] > 2 * 1024 * 1024) {
                throw new Exception("Le fichier " . $fichiers['name'][$key] . " est trop volumineux.");
            }

            // Vérifier le type MIME du fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            $fileType = $fichiers['type'][$key];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Type de fichier non autorisé pour " . $fichiers['name'][$key]);
            }

            // Déplacer le fichier vers le répertoire d'upload
            if (move_uploaded_file($tmpName, $targetFilePath)) {
                // Ajouter chaque fichier à la base de données
                $dao = new ReclamationDAO();
                $dao->addPieceJointe($reclamation_id, $fileName, $fichiers['type'][$key]);
            } else {
                throw new Exception("Erreur lors du téléchargement du fichier: " . $fichiers['name'][$key]);
            }
        }
    }
}

// Vérifier la requête
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $client_id = $_SESSION['client_id'] ?? 1;
    $type = htmlspecialchars($_POST['claim_type']);
    $description = htmlspecialchars($_POST['description']);
    $fichiers = $_FILES['attachments'] ?? [];

    $service = new ReclamationService();
    $service->ajouterReclamation($client_id, $type, $description, $fichiers);
}


    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "respond") {
        // Récupérer et valider les paramètres
        $reclamation_id = isset($_POST["reclamation_id"]) ? intval($_POST["reclamation_id"]) : 0;
        $response_text = isset($_POST["response_text"]) ? trim($_POST["response_text"]) : '';
        $claim_status = isset($_POST["claim_status"]) ? $_POST["claim_status"] : 'en_traitement';

        // Validation des données
        if ($reclamation_id <= 0 || empty($response_text)) {
            return "Erreur: ID de réclamation ou texte de réponse invalide.";
        }

        // Appeler la méthode traiterReponse dans ReclamationDAO
        if (!ReclamationDAO::traiterReponse($reclamation_id, $response_text, $claim_status)) {
            error_log("Échec du traitement de la réclamation ID: $reclamation_id, Statut: $claim_status, Réponse: $response_text");
            return "Erreur lors du traitement de la réclamation.";
        }

        // Rediriger en cas de succès
        header("Location: ../ihm/admin/claims-frs.php");
        exit;
    }

    return null; // Si la condition POST n'est pas remplie


// Traitement uniquement pour les requêtes GET
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $reclamationId = isset($_GET['reclamationId']) ? $_GET['reclamationId'] : null;

    if ($reclamationId && filter_var($reclamationId, FILTER_VALIDATE_INT)) {
        try {
            $reclamationDAO = new ReclamationDAO();
            $reclamation = $reclamationDAO->getReclamationById($reclamationId); // Appel à la nouvelle méthode
    
            if ($reclamation) {
                // Traitement de la réclamation récupérée
                $clientId = $reclamation->getClientId();  // Utilisation de la méthode de l'objet Reclamation pour récupérer le client_id
                $claimDescription = $reclamation->getDescription();
                $claimDate = $reclamation->getDateCreation();
                $claimType = ucfirst($reclamation->getType());
                
                // Récupérer les pièces jointes via la méthode `getPiecesJointes()` si disponible dans la classe `Reclamation`
                $piecesJointes = $reclamation->getPiecesJointes() ?: []; // On suppose qu'il y a une méthode getPiecesJointes
    
                $client = $reclamationDAO->getClientById($clientId);
    
                // Vérification simplifiée
                if ($client) {
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
    
                    if (!empty($piecesJointes)) {
                        foreach ($piecesJointes as $attachment) {
                            echo "<div class='attachment'>
                                    <i class='fas fa-paperclip'></i> <a href='/uploads/claims/$attachment' target='_blank'>$attachment</a>
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
    
    // Récupérer les réclamations lorsque la page est visitée sans POST
    $client_id = $_SESSION['client_id'];
    $reclamations = ReclamationDAO::getReclamationsByClientId($client_id);

    // Stocker les réclamations dans la session pour les utiliser dans la page d'affichage
    $_SESSION['reclamations'] = $reclamations;
}
?>