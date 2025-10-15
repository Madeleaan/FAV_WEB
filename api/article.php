<?php
$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'GET') {
    if (empty($_GET['login'])) {
        error(new ApiError(ApiErrorList::MISSING_PARAMS));
    } else {
        getArticle();
    }
} else if ($method == 'DELETE') {
    if (empty($_GET['id'])) {
        error(new ApiError(ApiErrorList::MISSING_PARAMS));
    } else {
        deleteArticle();
    }
} else if ($method == 'PUT') {
    if (empty($input['id']) || empty($input['title']) || empty($input['abstract'])) {
        error(new ApiError(ApiErrorList::MISSING_PARAMS));
    } else {
        updateArticle($input);
    }
} else {
    error(new ApiError(ApiErrorList::BAD_METHOD));
}

function getArticle(): void {
    $api = new API();
    $login = $_GET['login'];
    $res = $api->getUserArticles($login);

    if (is_array($res)) {
        echo json_encode($res);
    } else {
        error($res);
    }
}

function deleteArticle(): void {
    $api = new API();
    $id = $_GET['id'];
    $res = $api->deleteArticle($id);
    if ($res != null) {
        error($res);
    } else {
        echo json_encode(["status" => 200]);
    }
}

function updateArticle(array $input): void {
    $api = new API();
    $id = $input['id'];
    $title = $input['title'];
    $abstract = $input['abstract'];
    $res = $api->updateArticle($id, $title, $abstract);
    if ($res != null) {
        error($res);
    } else {
        echo json_encode(["status" => 200]);
    }
}