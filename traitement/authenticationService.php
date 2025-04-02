<?php
session_start();
require_once '../DB/connexion.php';

// Récupération et validation des données du formulaire
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header("Location: ../index.php?error=invalidCredentials");
    exit;
}
$_SESSION['user_id'] = 1; // Remplacement de id par user_id
$_SESSION['role'] = 'client';
header('Location: ../ihm/client/dashboard.php');

/*$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if ($user && $user['password_hash'] === $password) { // Utilisation de password_hash
    $_SESSION['user_id'] = $user['user_id']; // Remplacement de id par user_id
    $_SESSION['role'] = $user['role'];
    
    if ($user['role'] === 'ADMIN') {
        header('Location: ../ihm/admin/dashboard.php');
    } else {
        header('Location: ../ihm/client/dashboard.php');
    }
    exit;
} else {
    header("Location: ../index.php?error=invalidCredentials");
    exit;
} */

?>
