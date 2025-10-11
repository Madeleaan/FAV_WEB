<?php
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    if (empty($input['login']) || empty($input['password']) || empty($input['name'])) {
        error(new ApiError(ApiErrorList::MISSING_PARAMS));
    }

    if(strlen($input['password']) < 6) {
        error(new ApiError(ApiErrorList::WEAK_PASSWORD));
    }

    $api = new Api();
    $res = $api->createUser($input['login'], $input['password'], $input['name']);
    if ($res != null) {
        error($res);
    } else {
        json_encode(["status" => 200]);
    }
} else {
    error(new ApiError(ApiErrorList::BAD_METHOD));
}