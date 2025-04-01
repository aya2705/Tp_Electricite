<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header("Location: ../ihm/admin/clients.php");
    exit;
}

require_once '../DB/ClientDAO.php';

$client_id = $_POST['client_id'] ?? null;
$full_name = $_POST['full_name'] ?? '';
$phone     = $_POST['phone'] ?? '';
$address   = $_POST['address'] ?? '';

if (!$client_id || empty($full_name) || empty($phone) || empty($address)) {
    header("Location: ../ihm/admin/clients.php?error=emptyFields");
    exit;
}

try {
    $clientDAO = new ClientDAO();
    $clientDAO->updateClient($client_id, $full_name, $phone, $address);
    header("Location: ../ihm/admin/clients.php?success=clientEdited");
    exit;
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../ihm/admin/clients.php?error=editFailed");
    exit;
}
?>
