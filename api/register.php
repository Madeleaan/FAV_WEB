<?php
global $pdo;
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    register($pdo, $input);
} else {
    error(405, "Method $method is not allowed", "BAD_METHOD");
}

function register($pdo, $input) {
    if (empty($input['login']) || empty($input['password']) || empty($input['name'])) {
        error(400, 'Login, password and name are required', "NOT_ENOUGH_ARGUMENTS");
    }

    $sql = "SELECT * FROM users WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':login' => $input['login']]);
    if ($stmt->rowCount() > 0) error(400, 'Login already exists', "LOGIN_EXISTS");

    $sql = "INSERT INTO users (login, password, name) VALUES (:login, :password, :name)";
    $stmt = $pdo->prepare($sql);
    $passhash = password_hash($input['password'], PASSWORD_BCRYPT);
    $cleanName = htmlspecialchars($input['name']);
    $stmt->execute(['login' => $input['login'], 'password' => $passhash, 'name' => $cleanName]);
    echo json_encode(["status" => 200]);
}