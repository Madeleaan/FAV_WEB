<?php

require_once '../autoloader.inc.php';
use App\Models\AuthModel;
use App\Models\ModelError;
use App\Models\ModelException;

$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    if (empty($input['login']) || empty($input['password'])) {
        new ModelException(ModelError::MISSING_PARAMS);
    }

    $auth = new AuthModel();
    $res = $auth->loginUser($input['login'], $input['password']);
    if ($res != null) new ModelException($res);
} else {
    new ModelException(ModelError::BAD_METHOD);
}

echo json_encode(["status" => 200]);