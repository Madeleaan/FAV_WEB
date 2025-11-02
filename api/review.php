<?php

require_once '../autoloader.inc.php';
use App\Models\ModelError;
use App\Models\ModelException;
use App\Models\ReviewModel;

$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    if (empty($input['id']) ||empty($input['quality'] || empty('language') || empty('relevancy')))
        new ModelException(ModelError::MISSING_PARAMS);

    $reviewModel = new ReviewModel();
    $res = $reviewModel->addReview($input['id'], $input['quality'], $input['language'], $input['relevancy']);
    if ($res != null) new ModelException($res);
} else {
    new ModelException(ModelError::BAD_METHOD);
}

echo json_encode(["status" => 200]);