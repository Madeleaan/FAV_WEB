<?php
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    if (empty($input['login']) || empty($input['password'])) {
        error(new ApiError(ApiErrorList::MISSING_PARAMS));
    }

    $api = new API();
    $res = $api->loginUser($input['login'], $input['password']);
    if ($res != null) error($res);
} else {
    error(new ApiError(ApiErrorList::BAD_METHOD));
}