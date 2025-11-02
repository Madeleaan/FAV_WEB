<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\ReviewModel;
use App\Models\User;

/**
 * Controller, ktery vypise uvodni stranku
 */
class EditorReviewsController implements IController {
    private AuthModel $auth;
    private ReviewModel $reviewModel;

    public function __construct() {
        $this->auth = new AuthModel();
        $this->reviewModel = new ReviewModel();
    }

    /**
     * @param string $pageTitle Jmeno stranky
     * @return array    Vypis v sablone
     */
    public function show(string $pageTitle): array
    {
        $logged = $this->auth->getLoggedUser();
        $reviews = [];
        if ($logged instanceof User) {
            $reviews = $this->reviewModel->getUserReviews($logged->id);
        }


        return [
            "title" => $pageTitle,
            "logged" => $logged,
            "reviews" => $reviews
        ];
    }
}
