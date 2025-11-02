<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\ReviewModel;
use App\Models\UserModel;
use App\Models\AuthModel;
use App\Models\User;

/**
 * Controller, ktery vypise uvodni stranku
 */
class AdminArticlesController implements IController {
    private AuthModel $auth;
    private ArticleModel $articleModel;
    private UserModel $userModel;
    private ReviewModel $reviewModel;

    public function __construct() {
        $this->auth = new AuthModel();
        $this->articleModel = new ArticleModel();
        $this->userModel = new UserModel();
        $this->reviewModel = new ReviewModel();
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
            $articles = $this->articleModel->getAllArticles();
        }

        $editors = [];
        $reviews = [];

        if (is_array($articles)) {
            $editors = $this->userModel->getAvailableEditors($articles);
            $reviews = $this->reviewModel->getReviews($articles);
        }

        return [
            "title" => $pageTitle,
            "logged" => $logged,
            "articles" => $articles,
            "allEditors" => $editors,
            "allReviews" => $reviews
        ];
    }
}
