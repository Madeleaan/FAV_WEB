<?php
$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");

if ($method == 'GET') {
    $api = new API();
    if (empty($_GET['login'])) {
        error(new ApiError(ApiErrorList::MISSING_PARAMS));
    } else {
        $login = $_GET['login'];
        $res = $api->getUser($login);
        if ($res instanceof User) {
            echo json_encode ([
                "login" => $res->login,
                "name" => $res->name,
                "role" => $res->role,
                "enabled" => $res->enabled,
            ]);
        } else {
            error($res);
        }
    }
} else {
    error(new ApiError(ApiErrorList::BAD_METHOD));
}