<?php
global $pdo;
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    getUser($pdo, $_GET);
} else {
    error(405, "Method $method is not allowed");
}

function getUser($pdo, $input) {
    if (empty($input['login'])) {
        error(400, 'Login is required');
    }

    $sql = "SELECT name FROM users WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['login' => $input['login']]);
    if ($stmt->rowCount() == 0) error(400, 'Login does not exist');

    $data = $stmt->fetch();
    unset($data[0]);
    echo json_encode($data);
    return $data;
}