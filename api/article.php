<?php
$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");

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