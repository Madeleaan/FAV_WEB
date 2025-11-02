<?php

namespace App\Controllers;

use App\Models\ArticleModel;

/**
 * Controller, ktery vypise uvodni stranku
 */
class PublicArticlesController implements IController {
    /** Sprava databaze */
    private ArticleModel $articleModel;

    public function __construct() {
        $this->articleModel = new ArticleModel();
    }

    /**
     * @param string $pageTitle Jmeno stranky
     * @return array    Vypis v sablone
     */
    public function show(string $pageTitle): array
    {
        return [
            "title" => $pageTitle,
            "articles" => $this->articleModel->getPublicArticles()
        ];
    }
}
