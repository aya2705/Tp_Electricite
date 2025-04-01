<?php
session_start();
require_once '../DB/connexion.php';
require_once '../models/Client.php';
require_once '../DB/ClientDAO.php';

$full_name = $_POST['full_name'] ?? '';
$phone     = $_POST['phone'] ?? '';
$address   = $_POST['address'] ?? '';

if (empty($full_name) || empty($phone) || empty($address)) {
    header("Location: ../ihm/admin/clients.php?error=emptyFields");
    exit;
}

try {
    $clientDAO = new ClientDAO();
    $clientDAO->createClient($full_name, $phone, $address);
    header("Location: ../ihm/admin/clients.php?success=clientAdded");
    exit;
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../ihm/admin/clients.php?error=clientFailed");
    exit;
}
?>
