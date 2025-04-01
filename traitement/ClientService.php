<?php
session_start();
require_once '../DB/ClientDAO.php';
require_once '../DB/compteurDAO.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header("Location: ../ihm/admin/clients.php");
    exit;
}

$action = $_REQUEST['action'] ?? '';

$clientDAO = new ClientDAO();
$compteurDAO = new CompteurDAO();

switch($action) {
    case 'add':
        $full_name = $_POST['full_name'] ?? '';
        $phone     = $_POST['phone'] ?? '';
        $address   = $_POST['address'] ?? '';
        $compteursInput = $_POST['compteurs'] ?? '';

        if (empty($full_name) || empty($phone) || empty($address)) {
            header("Location: ../ihm/admin/clients.php?error=emptyFields");
            exit;
        }

        try {
            $newClientId = $clientDAO->createClient($full_name, $phone, $address);
            if (!empty($compteursInput)) {
                $compteursArray = array_filter(array_map('trim', explode(',', $compteursInput)));
                foreach ($compteursArray as $numero) {
                    $compteurDAO->createCompteur($newClientId, $numero);
                }
            }
            header("Location: ../ihm/admin/clients.php?success=clientAdded");
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            header("Location: ../ihm/admin/clients.php?error=clientFailed");
            exit;
        }
        break;

    case 'edit':
        $client_id = $_POST['client_id'] ?? null;
        $full_name = $_POST['full_name'] ?? '';
        $phone     = $_POST['phone'] ?? '';
        $address   = $_POST['address'] ?? '';
        $compteursInput = $_POST['compteurs'] ?? '';

        if (!$client_id || empty($full_name) || empty($phone) || empty($address)) {
            header("Location: ../ihm/admin/clients.php?error=emptyFields");
            exit;
        }

        try {
            $clientDAO->updateClient($client_id, $full_name, $phone, $address);
            $compteurDAO->deleteCompteursByClientId($client_id);
            if (!empty($compteursInput)) {
                $compteursArray = array_filter(array_map('trim', explode(',', $compteursInput)));
                foreach ($compteursArray as $numero) {
                    $compteurDAO->createCompteur($client_id, $numero);
                }
            }
            header("Location: ../ihm/admin/clients.php?success=clientEdited");
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            header("Location: ../ihm/admin/clients.php?error=editFailed");
            exit;
        }
        break;

    case 'delete':
        $client_id = $_GET['client_id'] ?? null;
        if (!$client_id) {
            header("Location: ../ihm/admin/clients.php?error=missingId");
            exit;
        }

        try {
            $compteurDAO->deleteCompteursByClientId($client_id);
            $clientDAO->deleteClient($client_id);
            header("Location: ../ihm/admin/clients.php?success=clientDeleted");
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            header("Location: ../ihm/admin/clients.php?error=deleteFailed");
            exit;
        }
        break;

    default:
        header("HTTP/1.0 400 Bad Request");
        echo "Action non valide.";
        exit;
}
?>
