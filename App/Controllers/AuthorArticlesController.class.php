<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\AuthModel;
use App\Models\User;

/**
 * Controller, ktery vypise uvodni stranku
 */
class AuthorArticlesController implements IController {
    private AuthModel $auth;
    private ArticleModel $articleModel;

    public function __construct() {
        $this->auth = new AuthModel();
        $this->articleModel = new ArticleModel();
    }

    /**
     * @param string $pageTitle Jmeno stranky
     * @return array    Vypis v sablone
     */
    public function show(string $pageTitle): array
    {
        $logged = $this->auth->getLoggedUser();
        $articles = [];
        if ($logged instanceof User) {
            $articles = $this->articleModel->getUserArticles($logged->id);
        }


        return [
            "title" => $pageTitle,
            "logged" => $logged,
            "articles" => $articles
        ];
    }
}
