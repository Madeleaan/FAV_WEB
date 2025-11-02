<?php

namespace App\Models;

enum ModelError {
    case BAD_METHOD;
    case MISSING_PARAMS;
    case WEAK_PASSWORD;
    case LOGIN_EXISTS;
    case BAD_LOGIN;
    case BAD_PASS;
    case BAD_TASK;
    case INTERNAL_ERROR;
    case DISABLED_USER;
    case NO_ACCESS;
    case ARTICLE_PUBLIC;
    case BAD_FILE;
    case REVIEW_EXISTS;
    case ILLEGAL_LOGIN;
    case NO_REVIEWS;
    case BAD_SCORING;
    case BAD_ID;
}