<?php
session_start();
require_once '../DB/connexion.php';
require_once '../models/user.php';
require_once '../DB/userDAO.php';

$userDAO = new UserDAO();
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: ../index.php?error=emptyFields");
    exit;
}

try {
    $user = $userDAO->getUserByEmail($email);
    if ($user && password_verify($password, $user->getPasswordHash())) {
        $_SESSION['user_id'] = $user->getUserId();
        $_SESSION['role'] = $user->getRole();
        $redirect = $user->getRole() === 'fournisseur' ? '../ihm/admin/dashboard.php' : '../ihm/client/dashboard.php';
        header("Location: $redirect");
        exit;
    } else {
        header("Location: ../index.php?error=invalidCredentials");
        exit;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../index.php?error=databaseError");
    exit;
}