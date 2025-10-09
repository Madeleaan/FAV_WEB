<?php
global $pdo;
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    logout();
} else {
    error(405, "Method $method is not allowed", "BAD_METHOD");
}

function logout() {
    session_start();
    $_SESSION['login'] = null;
}