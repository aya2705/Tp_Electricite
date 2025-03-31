<?php
session_start();
require_once '../DB/connexion.php';
require_once '../models/user.php';
require_once '../DB/UserDAO.php';
require_once '../models/client.php';
require_once '../DB/ClientDAO.php';

$clientDAO = new ClientDAO();
$userDAO = new UserDAO();

$clientId = $_POST['client_id'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($clientId) || empty($email) || empty($password)) {
    header("Location: ../ihm/inscription.php?error=emptyFields");
    exit;
}

try {
    $client = $clientDAO->getClientById($clientId);
    if (!$client) {
        header("Location: ../ihm/inscription.php?error=clientNotFoundOrLinked");
        exit;
    }

    if ($userDAO->getUserByEmail($email)) {
        header("Location: ../ihm/inscription.php?error=emailExists");
        exit;
    }

    $userId = $userDAO->createUser($email, $password);
    $clientDAO->linkUserToClient($client->getClientId(), $userId);
    
    header("Location: ../index.php?success=registered");
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../ihm/inscription.php?error=registrationFailed");
    exit;
}