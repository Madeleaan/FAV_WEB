<?php

function error(ApiError $error): void
{
    http_response_code($error->getCode());
    echo json_encode(["msg" => $error->getMessage(), "error" => $error->getCode()]);
    die();
}

enum ApiErrorList {
    case BAD_METHOD;
    case MISSING_PARAMS;
    case WEAK_PASSWORD;
    case LOGIN_EXISTS;
    case BAD_LOGIN;
    case BAD_PASS;
    case BAD_TASK;
    case DB_ERROR;
    case DISABLED_USER;
    case NO_ACCESS;
    case ARTICLE_PUBLIC;
    case BAD_FILE;
    case REVIEW_EXISTS;
    case ILLEGAL_LOGIN;
    case NO_REVIEWS;
}

class ApiError extends Exception {
    function __construct(ApiErrorList $error) {
        match ($error) {
            ApiErrorList::BAD_METHOD => parent::__construct('Unsupported method', 405),
            ApiErrorList::MISSING_PARAMS => parent::__construct('Some parameters are missing', 400),
            ApiErrorList::WEAK_PASSWORD => parent::__construct('Password is too weak, must be 6 characters', 403),
            ApiErrorList::LOGIN_EXISTS => parent::__construct('Login already exists', 400),
            ApiErrorList::BAD_LOGIN => parent::__construct('Login does not exist', 400),
            ApiErrorList::BAD_PASS => parent::__construct('Password is incorrect', 403),
            ApiErrorList::BAD_TASK => parent::__construct('Admin task is not defined', 400),
            ApiErrorList::DB_ERROR => parent::__construct('Database error', 500),
            ApiErrorList::DISABLED_USER => parent::__construct('Disabled user', 403),
            ApiErrorList::NO_ACCESS => parent::__construct('No access', 403),
            ApiErrorList::ARTICLE_PUBLIC => parent::__construct('Article is public, cant edit or delete it', 403),
            ApiErrorList::BAD_FILE => parent::__construct('Submitted file is in a bad format', 400),
            ApiErrorList::REVIEW_EXISTS => parent::__construct('Review already exists', 400),
            ApiErrorList::ILLEGAL_LOGIN => parent::__construct('Illegal characters in login', 400),
            ApiErrorList::NO_REVIEWS => parent::__construct('Article does not have enough reviews', 400),
        };
    }
}