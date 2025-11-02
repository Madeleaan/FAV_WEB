<?php

include_once '../autoloader.inc.php';

use App\Models\ArticleModel;
use App\Models\ModelError;
use App\Models\ModelException;
use App\Models\ReviewModel;
use App\Models\UserModel;

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
header("Content-Type: application/json");

$userModel = new UserModel();
$articleModel = new ArticleModel();
$reviewModel = new ReviewModel();

if ($method == 'POST') {
    if (empty($input['task'])) {
        new ModelException(ModelError::MISSING_PARAMS);
    }

    switch ($input['task']) {
        case 'toggle-user':
            if (empty($input['login'])) new ModelException(ModelError::MISSING_PARAMS);

            $res = $userModel->toggleUser($input['login']);
            if ($res != null) new ModelException($res);
            break;
        case 'delete-user':
            if (empty($input['login'])) new ModelException(ModelError::MISSING_PARAMS);

            $res = $userModel->deleteUser($input['login']);
            if ($res != null) new ModelException($res);
            break;
        case 'change-role':
            if (empty($input['login']) || empty($input['role'])) new ModelException(ModelError::MISSING_PARAMS);

            $res = $userModel->changeRole($input['login'], $input['role']);
            if ($res != null) new ModelException($res);
            break;
        case 'add-editor':
            if (empty($input['article']) || empty($input['editor'])) new ModelException(ModelError::MISSING_PARAMS);

            $res = $articleModel->addEditor($input['article'], $input['editor']);
            if ($res != null) new ModelException($res);
            break;
        case 'delete-review':
            if (empty($input['id'])) new ModelException(ModelError::MISSING_PARAMS);

            $res = $reviewModel->deleteReview($input['id']);
            if ($res != null) new ModelException($res);
            break;
        case 'accept-article':
            if (empty($input['id']) || empty($input['accept'])) new ModelException(ModelError::MISSING_PARAMS);

            $res = $articleModel->acceptArticle($input['id'], $input['accept']);
            if ($res != null) new ModelException($res);
            break;
        default:
            new ModelException(ModelError::BAD_TASK);
    }
} else {
    new ModelException(ModelError::BAD_METHOD);
}

echo json_encode(["status" => 200]);