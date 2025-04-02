<?php
session_start();

// here i set manually a session so i can authenticate
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'client';
// with an authentication system i should log in then if my account is registered then i will go the clients dashboard
header('Location: ihm/client/dashboard.php');