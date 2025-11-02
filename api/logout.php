<?php

require_once '../autoloader.inc.php';
use App\Models\AuthModel;
use App\Models\ModelError;
use App\Models\ModelException;

$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");

if ($method == 'POST') {
    $auth = new AuthModel();
    $auth->logoutUser();
} else {
    new ModelException(ModelError::BAD_METHOD);
}

echo json_encode(["status" => 200]);