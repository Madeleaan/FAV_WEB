<?php
session_start();

global $pdo;
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    login($pdo, $input);
} else {
    error(405, "Method $method is not allowed", "BAD_METHOD");
}

function login($pdo, $input) {
    if (empty($input['login']) || empty($input['password'])) {
        error(400, 'Login and password are required', "NOT_ENOUGH_ARGUMENTS");
    }

    $sql = "SELECT password FROM users WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['login' => $input['login']]);
    if ($stmt->rowCount() == 0) error(400, 'Login does not exist', "BAD_LOGIN");

    $pass = $stmt->fetch()[0];
    if (password_verify($input['password'], $pass)) {
        $_SESSION['login'] = $input['login'];
        echo json_encode(['status' => 200]);
    } else {
        error(400, 'Login and password are incorrect', "BAD_PASS");
    }
}