<?php
$method = $_SERVER['REQUEST_METHOD'];
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);

if ($method == 'POST') {
    if (empty($input['id']) ||empty($input['quality'] || empty('language') || empty('relevancy')))
        error(new ApiError(ApiErrorList::MISSING_PARAMS));

    $api = new API();
    $res = $api->addReview($input['id'], $input['quality'], $input['language'], $input['relevancy']);

    if ($res != null) error($res);
    else echo json_encode(['status' => 200]);
} else {
    error(new ApiError(ApiErrorList::BAD_METHOD));
}