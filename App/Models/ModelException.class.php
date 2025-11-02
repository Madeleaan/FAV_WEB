<?php

namespace App\Models;

use Exception;

class ModelException extends Exception {
    private ModelError $err;

    function __construct(ModelError $error) {
        $this->err = $error;
        match ($error) {
            ModelError::BAD_METHOD => parent::__construct('Unsupported method', 405),
            ModelError::MISSING_PARAMS => parent::__construct('Some parameters are missing', 400),
            ModelError::WEAK_PASSWORD => parent::__construct('Password is too weak, must be 6 characters', 403),
            ModelError::LOGIN_EXISTS => parent::__construct('Login already exists', 400),
            ModelError::BAD_LOGIN => parent::__construct('Login does not exist', 400),
            ModelError::BAD_PASS => parent::__construct('Password is incorrect', 403),
            ModelError::BAD_TASK => parent::__construct('Admin task is not defined', 400),
            ModelError::INTERNAL_ERROR => parent::__construct('Internal error', 500),
            ModelError::DISABLED_USER => parent::__construct('Disabled user', 403),
            ModelError::NO_ACCESS => parent::__construct('No access', 403),
            ModelError::ARTICLE_PUBLIC => parent::__construct('Article is public, cant edit or delete it', 403),
            ModelError::BAD_FILE => parent::__construct('Submitted file is in a bad format', 400),
            ModelError::REVIEW_EXISTS => parent::__construct('Review already exists', 400),
            ModelError::ILLEGAL_LOGIN => parent::__construct('Illegal characters in login', 400),
            ModelError::NO_REVIEWS => parent::__construct('Article does not have enough reviews', 400),
            ModelError::BAD_SCORING => parent::__construct('Review scoring must be between 1 and 5', 400),
            ModelError::BAD_ID => parent::__construct('Bad id', 400),
        };

        http_response_code($this->getCode());
        echo json_encode([
            "msg" => $this->getMessage(),
            "error" => $this->getCode(),
            "code" => $this->getError()->name
        ]);
        die();
    }

    function getError(): ModelError { return $this->err; }
}