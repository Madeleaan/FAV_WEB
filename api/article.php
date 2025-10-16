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
} else if ($method == 'POST') {
    if (empty($_POST['title']) || empty($_POST['abstract']) || empty($_FILES['file'])) {
        error(new ApiError(ApiErrorList::MISSING_PARAMS));
    }  else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $ftype = finfo_file($finfo, $_FILES['file']['tmp_name']);
        finfo_close($finfo);

        if ($ftype != 'application/pdf') error(new ApiError(ApiErrorList::BAD_FILE));
        else postArticle($_POST, $_FILES['file']);
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

function postArticle(array $input, array $file): void {
    $dir = "../storage/articles/";
    do {
        $orig = explode('.', $file['name']);
        $filename = bin2hex(random_bytes(16)) . '.' . end($orig);
    } while (file_exists($dir . $filename));
    move_uploaded_file($file['tmp_name'], $dir . $filename);

    $api = new API();
    $api->postArticle($input['title'], $input['abstract'], $filename);
}