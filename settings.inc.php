<?php
//// Pripojeni k DB ////

/** DB server */

use App\Controllers;
use App\Views;

const DB_SERVER = "localhost";
/** Nazev DB */
const DB_NAME = "web_sem";
/** DB uzivatel */
const DB_USER = "root";
/** Heslo uzivatele */
const DB_PASS = "";

//// Tabulky ////

/** Tabulka s uzivateli */
const TABLE_USERS = "users";
/** Tabulka se clanky */
const TABLE_ARTICLES = "articles";
/** Tabulka s recenzemi */
const TABLE_REVIEWS = "reviews";

//// Dostupne stranky ////

/** Vychozi stranka */
const DEFAULT_WEBPAGE = "uvod";
/** Stranky */
const WEB_PAGES = [
    "uvod" =>[
        "title" => "Úvodní stránka",

        "controller_class_name" => Controllers\IntroductionController::class,
        "view_class_name" => Views\TemplateBasics::class,
        "template_type" => Views\TemplateBasics::PAGE_INTRODUCTION
    ]
];