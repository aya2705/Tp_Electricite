<?php
session_start();
require_once '../DB/connexion.php';

$clientId = trim($_POST['client_id'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($clientId) || empty($email) || empty($password)) {
    header("Location: ../ihm/inscription.php?error=emptyFields");
    exit;
}

$conn = Database::getInstance()->getConnection();

// Vérifier que le client existe et n'est pas déjà lié
$stmt = $conn->prepare("SELECT client_id FROM clients WHERE client_id = :client_id AND user_id IS NULL LIMIT 1");
$stmt->execute(['client_id' => $clientId]);
$client = $stmt->fetch();
if (!$client) {
    header("Location: ../ihm/inscription.php?error=clientNotFoundOrLinked");
    exit;
}

// Vérifier si l'email existe déjà dans la table users
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    header("Location: ../ihm/inscription.php?error=emailExists");
    exit;
}

// Insérer le nouvel utilisateur (utilisez password_hash() en production)
$stmt = $conn->prepare("INSERT INTO users (email, password_hash, role) VALUES (:email, :password, 'client')");
$result = $stmt->execute([
    'email'    => $email,
    'password' => $password
]);

if ($result) {
    $userId = $conn->lastInsertId();
    
    // Lier l'utilisateur au client existant
    $stmt = $conn->prepare("UPDATE clients SET user_id = :user_id WHERE client_id = :client_id");
    $stmt->execute([
        'user_id'   => $userId,
        'client_id' => $clientId
    ]);
    
    header("Location: ../index.php?success=registered");
    exit;
} else {
    header("Location: ../ihm/inscription.php?error=registrationFailed");
    exit;
}
?>
