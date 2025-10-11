<?php
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
header("Content-Type: application/json");

if ($method == 'GET') {
    if (empty($_GET['task'])) error(new ApiError(ApiErrorList::MISSING_PARAMS));
    switch ($_GET['task']) {
        case 'list-users':
            listUsers();
            break;
        default:
            error(new ApiError(ApiErrorList::BAD_TASK));
    }
} else if ($method == 'POST') {
    if (empty($input['task'])) error(new ApiError(ApiErrorList::MISSING_PARAMS));
    switch ($input['task']) {
        case 'toggle-user':
            if (empty($input['login'])) error(new ApiError(ApiErrorList::MISSING_PARAMS));
            toggleUser($input['login']);
            break;
        default:
            error(new ApiError(ApiErrorList::BAD_TASK));
    }
} else {
    error(new ApiError(ApiErrorList::BAD_METHOD));
}

function listUsers(): void {
    $api = new API();
    $users = $api->listUsers();
    if (is_array($users)) {
        echo json_encode($api->listUsers());
    } else {
        error($users);
    }
}

function toggleUser(string $login): void {
    $api = new API();
    $res = $api->toggleUser($login);
    if ($res != null) error($res);
    else echo json_encode(["status" => 200]);
}