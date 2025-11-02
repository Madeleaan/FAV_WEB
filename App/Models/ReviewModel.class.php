<?php

namespace App\Models;

class ReviewModel {
    private DatabaseModel $db;
    private UserModel $userModel;
    private ArticleModel $articleModel;
    private AuthModel $auth;

    public function __construct() {
        $this->db = new DatabaseModel();
        $this->userModel = new UserModel();
        $this->articleModel = new ArticleModel();
        $this->auth = new AuthModel();
    }

    /**
     * Returns reviews that belong to a user
     * @param int $id   user id
     * @return Review[]
     */
    public function getUserReviews(int $id): array {
        if (!$this->userModel->getUser($id) instanceof User) return [];

        $sql = "SELECT * FROM reviews WHERE editor = :id ORDER BY id DESC";
        $data = $this->db->fetchAllSQL($sql, ['id' => $id]);

        $res = [];
        foreach ($data as $review) $res[] = new Review($review, $this->userModel, $this->articleModel);

        return $res;
    }

    public function addReview(int $id, float $quality, float $language, float $relevancy): null | ModelError {
        $logged = $this->auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role != Role::EDITOR)
            return ModelError::NO_ACCESS;

        if ($quality < 1 || $quality > 5 || $language < 1 || $language > 5 || $relevancy < 1 || $relevancy > 5)
            return ModelError::BAD_SCORING;

        $sql = "SELECT * FROM reviews WHERE id = :id";
        $data = $this->db->fetchSQL($sql, ["id" => $id]);

        if ($data == null) return ModelError::BAD_ID;
        $review = new Review($data, $this->userModel, $this->articleModel);

        if ($review->editor->id != $logged->id)
            return ModelError::NO_ACCESS;
        if ($review->article->status != 'waiting')
            return ModelError::ARTICLE_PUBLIC;

        $quality = $quality - fmod($quality, 0.5);
        $language = $language - fmod($language, 0.5);
        $relevancy = $relevancy - fmod($relevancy, 0.5);
        $sql = "UPDATE reviews SET quality = :quality, language = :language, relevancy = :relevancy WHERE id = :id";
        $this->db->fetchSQL($sql, ["quality" => $quality, "language" => $language, "relevancy" => $relevancy, "id" => $id]);

        return null;
    }

    /**
     * Admin only - Gets all reviews for an array of articles
     * @param Article[] $articles   array of articles
     * @return array|ModelError array of article ids that contain array of reviews
     */
    public function getReviews(array $articles): array | ModelError {
        if (!$articles) return ModelError::MISSING_PARAMS;

        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN->value) {
            return ModelError::NO_ACCESS;
        }

        $res = [];
        $sql = "SELECT * FROM reviews WHERE article = :article";

        foreach ($articles as $article) {
            $data = $this->db->fetchAllSQL($sql, ["article" => $article->id]);

            if ($data != null) {
                $reviews = [];
                foreach ($data as $review) {
                    $reviews[] = new Review($review, $this->userModel, $this->articleModel);
                }
                $res[$article->id] = $reviews;
            }
        }

        return $res;
    }

    /**
     * Admin only - delete a review
     * @param int $id   id of the review
     * @return ModelError|null  null if the operation was succesful, otherwise ModelError
     */
    public function deleteReview(int $id): null | ModelError {
        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN->value) {
            return ModelError::NO_ACCESS;
        }

        $sql = "DELETE FROM reviews WHERE id = :id";
        $this->db->fetchSQL($sql, ["id" => $id]);

        return null;
    }
}

class Review {
    public int $id;
    public Article $article;
    public User $editor;
    public float $quality;
    public float $language;
    public float $relevancy;

    function __construct(array $data, UserModel $userModel, ArticleModel $articleModel) {
        $this->id = $data["id"];
        $this->article = $articleModel->getArticle($data["article"]);
        $this->editor = $userModel->getUser($data["editor"]);
        $this->quality = $data["quality"];
        $this->language = $data["language"];
        $this->relevancy = $data["relevancy"];
    }
}