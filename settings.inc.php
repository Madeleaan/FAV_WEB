<?php

use App\Controllers;
use App\Views;

//// Dostupne stranky ////

/** Vychozi stranka */
const DEFAULT_WEBPAGE = "uvod";
/** Stranky */
const WEB_PAGES = [
    "uvod" => [
        "title" => "Úvodní stránka",

        "controller_class_name" => Controllers\IntroductionController::class,
        "template_type" => Views\TemplateBasics::PAGE_INTRODUCTION
    ],

    "clanky" => [
        "title" => "Veřejné články",

        "controller_class_name" => Controllers\PublicArticlesController::class,
        "template_type" => Views\TemplateBasics::PAGE_PUBLIC_ARTICLES
    ],

    "login" => [
        "title" => "Přihlášení",

        "controller_class_name" => Controllers\LoginController::class,
        "template_type" => Views\TemplateBasics::PAGE_LOGIN
    ],

    "author/articles" => [
        "title" => "Moje články",

        "controller_class_name" => Controllers\AuthorArticlesController::class,
        "template_type" => Views\TemplateBasics::PAGE_AUTHOR_ARTICLES
    ],

    "editor/reviews" => [
        "title" => "Moje recenze",

        "controller_class_name" => Controllers\EditorReviewsController::class,
        "template_type" => Views\TemplateBasics::PAGE_EDITOR_REVIEWS
    ],

    "admin/users" => [
        "title" => "Správa uživatelů",

        "controller_class_name" => Controllers\AdminUsersController::class,
        "template_type" => Views\TemplateBasics::PAGE_ADMIN_USERS
    ],

    "admin/articles" => [
        "title" => "Správa článků",

        "controller_class_name" => Controllers\AdminArticlesController::class,
        "template_type" => Views\TemplateBasics::PAGE_ADMIN_ARTICLES
    ]
];