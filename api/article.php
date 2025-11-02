<?php

require_once '../autoloader.inc.php';

use App\Models\ArticleModel;
use App\Models\ModelError;
use App\Models\ModelException;
use Random\RandomException;

$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);
$articleModel = new ArticleModel();

if ($method == 'DELETE') {
    if (empty($_GET['id'])) {
        new ModelException(ModelError::MISSING_PARAMS);
    }

    $res = $articleModel->deleteArticle($_GET['id']);
    if ($res != null) new ModelException($res);
} else if ($method == 'PUT') {
    if (empty($input['id']) || empty($input['title']) || empty($input['abstract'])) {
        new ModelException(ModelError::MISSING_PARAMS);
    }

    $res = $articleModel->updateArticle($input['id'], $input['title'], $input['abstract']);
    if ($res != null) new ModelException($res);
} else if ($method == 'POST') {
    if (empty($_POST['title']) || empty($_POST['abstract']) || empty($_FILES['file'])) {
        new ModelException(ModelError::MISSING_PARAMS);
    }

    $dir = "../storage/articles/";
    $file = $_FILES['file'];
    do {
        $orig = explode('.', $file['name']);
        try {
            $filename = bin2hex(random_bytes(16)) . '.' . end($orig);
        } catch (RandomException $e) {
            new ModelException(ModelError::DB_ERROR);
        }
    } while (file_exists($dir . $filename));
    move_uploaded_file($file['tmp_name'], $dir . $filename);

    $res = $articleModel->postArticle($_POST['title'], $_POST['abstract'], $filename);
    if ($res != null) new ModelException($res);
} else {
    new ModelException(ModelError::BAD_METHOD);
}

echo json_encode(["status" => 200]);