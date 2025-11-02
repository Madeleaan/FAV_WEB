<?php

require_once '../autoloader.inc.php';
use App\Models\ModelError;
use App\Models\ModelException;
use App\Models\UserModel;

$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");

if ($method == 'GET') {
    if (empty($_GET['id'])) {
        new ModelException(ModelError::MISSING_PARAMS);
    }

    $userModel = new UserModel();
    $res = $userModel->getUser($_GET['id']);

    if ($res == null) {
        new ModelException(ModelError::BAD_ID);
    }

    echo json_encode([
        'login' => $res->login,
        'name' => $res->name,
        'role' => $res->role,
        'enabled' => $res->enabled,
    ]);
} else {
    new ModelException(ModelError::BAD_METHOD);
}