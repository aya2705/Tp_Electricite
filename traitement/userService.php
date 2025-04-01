<?php
session_start();
require_once '../DB/connexion.php';
require_once '../models/user.php';
require_once '../DB/userDAO.php';
require_once '../DB/ClientDAO.php';

$action = $_REQUEST['action'] ?? '';

$userDAO = new UserDAO();
$clientDAO = new ClientDAO();

switch ($action) {
    case 'authenticate':
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
        break;

    case 'register':
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
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            header("Location: ../ihm/inscription.php?error=registrationFailed");
            exit;
        }
        break;

    default:
        header("HTTP/1.0 400 Bad Request");
        echo "Action non valide.";
        exit;
}