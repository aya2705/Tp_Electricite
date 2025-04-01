<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header("Location: ../ihm/admin/clients.php");
    exit;
}
require_once '../DB/ClientDAO.php';

$client_id = $_GET['client_id'] ?? null;
if (!$client_id) {
    header("Location: ../ihm/admin/clients.php?error=missingId");
    exit;
}

try {
    $clientDAO = new ClientDAO();
    $clientDAO->deleteClient($client_id);
    header("Location: ../ihm/admin/clients.php?success=clientDeleted");
    exit;
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../ihm/admin/clients.php?error=deleteFailed");
    exit;
}
?>
