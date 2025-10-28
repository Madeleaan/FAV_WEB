<?php
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    logout();
} else {
    error(new ApiError(ApiErrorList::BAD_METHOD));
}

function logout(): void {
    $_SESSION['login'] = null;
    session_destroy();
}