<?php

require_once '../autoloader.inc.php';
use App\Models\AuthModel;
use App\Models\ModelError;
use App\Models\ModelException;

$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    if (empty($input['login']) || empty($input['password']) || empty($input['name'])) {
        new ModelException(ModelError::MISSING_PARAMS);
    }

    if(strlen($input['password']) < 6) {
        new ModelException(ModelError::WEAK_PASSWORD);
    }

    $exp = "/^\w+$/";
    if (!preg_match($exp, $input['login'])) {
        new ModelException(ModelError::ILLEGAL_LOGIN);
    }

    $auth = new AuthModel();
    $res = $auth->createUser($input['login'], $input['password'], $input['name']);
    if ($res != null) new ModelException($res);
} else {
    new ModelException(ModelError::BAD_METHOD);
}

echo json_encode(["status" => 200]);