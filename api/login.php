<?php
session_start();

global $pdo;
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    login($pdo, $input);
} else {
    error(405, "Method $method is not allowed");
}

function login($pdo, $input) {
    if (empty($input['login']) || empty($input['password'])) {
        error(400, 'Login and password are required');
    }

    $sql = "SELECT password FROM users WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['login' => $input['login']]);
    if ($stmt->rowCount() == 0) error(400, 'Login does not exist');

    $pass = $stmt->fetch()[0];
    if ($pass == $input['password']) {
        $_SESSION['login'] = $input['login'];
    } else {
        error(400, 'Login and password are incorrect');
    }
}