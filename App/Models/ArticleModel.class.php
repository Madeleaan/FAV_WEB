<?php

namespace App\Models;

use DateTime;

class ArticleModel {
    private DatabaseModel $db;
    private UserModel $userModel;
    private AuthModel $auth;

    public function __construct() {
        $this->db = new DatabaseModel();
        $this->userModel = new UserModel();
        $this->auth = new AuthModel();
    }

    /**
     * Returns all public articles
     * @return Article[]
     */
    public function getPublicArticles(): array {
        $sql = "SELECT * FROM articles WHERE status = 'accepted' ORDER BY date DESC";
        $data = $this->db->fetchAllSQL($sql, []);

        $res = [];
        foreach ($data as $review) $res[] = new Article($review, $this->userModel);

        return $res;
    }

    /**
     * Returns articles that belong to a user
     * @param int $id   user id
     * @return Article[]
     */
    public function getUserArticles(int $id): array {
        if (!$this->userModel->getUser($id) instanceof User) return [];

        $sql = "SELECT * FROM articles WHERE author = :id ORDER BY date DESC";
        $data = $this->db->fetchAllSQL($sql, ['id' => $id]);

        $res = [];
        foreach ($data as $review) $res[] = new Article($review, $this->userModel);

        return $res;
    }

    /**
     * Returns an article based on id
     * @param int $id   id of the article
     * @return Article|null
     */
    public function getArticle(int $id): Article | null {
        $sql = "SELECT * FROM articles WHERE id = :id";
        $data = $this->db->fetchSQL($sql, ['id' => $id]);

        if ($data == null) return null;
        else return new Article($data, $this->userModel);
    }

    /**
     * Deletes an article
     * @param int $id   id of the article
     * @return ModelError|null null if the operation was succesful, otherwise ModelError
     */
    public function deleteArticle(int $id): null | ModelError {
        $sql = "SELECT * FROM articles WHERE id = :id";
        $data = $this->db->fetchSQL($sql, ['id' => $id]);

        // article does not exist
        if ($data == null) return ModelError::BAD_ID;
        $article = new Article($data, $this->userModel);

        // article does not belong to logged user
        $logged = $this->auth->getLoggedUser();
        if (!$logged instanceof User || $article->author->id != $logged->id) return ModelError::NO_ACCESS;

        //article is public
        if ($article->status == 'accepted') return ModelError::ARTICLE_PUBLIC;

        $sql = "DELETE FROM articles WHERE id = :id";
        $this->db->fetchSQL($sql, ['id' => $id]);

        unlink("../storage/articles/" . $article->file);
        return null;
    }

    /**
     * Update article data
     * @param int $id   id of the article
     * @param string $title new title
     * @param string $abstract new abstract
     * @return ModelError|null null if the operation was succesful, otherwise ModelError
     */
    public function updateArticle(int $id, string $title, string $abstract): null | ModelError {
        if ($title == "" || $abstract == "") return ModelError::MISSING_PARAMS;

        $sql = "SELECT * FROM articles WHERE id = :id";
        $data = $this->db->fetchSQL($sql, ['id' => $id]);

        // article does not exist
        if ($data == null) return ModelError::BAD_ID;
        $article = new Article($data, $this->userModel);

        // article does not belong to logged user
        $logged = $this->auth->getLoggedUser();
        if (!$logged instanceof User || $article->author->id != $logged->id) return ModelError::NO_ACCESS;

        //article is public
        if ($article->status == 'accepted') return ModelError::ARTICLE_PUBLIC;

        $sql = "UPDATE articles SET title = :title, abstract = :abstract WHERE id = :id";
        $this->db->fetchSQL($sql, ['id' => $id, 'title' => $title, 'abstract' => $abstract]);

        return null;
    }

    /**
     * Posts a new article
     * @param string $title title of the article
     * @param string $abstract abstract of the article
     * @param string $file  name of the file
     * @return ModelError|null  null if the operation was succesful, otherwise ModelError
     */
    public function postArticle(string $title, string $abstract, string $file): null | ModelError {
        if ($title == "" || $abstract == "" || $file == "") return ModelError::MISSING_PARAMS;

        $logged = $this->auth->getLoggedUser();
        if (!$logged instanceof User) return ModelError::NO_ACCESS;

        $sql = "INSERT INTO articles (title, abstract, file, author) values (:title, :abstract, :file, :author)";
        $this->db->fetchSQL($sql, [
            'title' => $title,
            'abstract' => $abstract,
            'file' => $file,
            'author' => $logged->id
        ]);

        return null;
    }

    /**
     * Admin only - gets all articles
     * @return Article[]|ModelError
     */
    public function getAllArticles(): array | ModelError {
        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN->value)
            return ModelError::NO_ACCESS;

        $sql = "SELECT * FROM articles ORDER BY date DESC";
        $data = $this->db->fetchAllSQL($sql, []);

        $res = [];
        foreach ($data as $article) $res[] = new Article($article, $this->userModel);
        return $res;
    }

    /**
     * Admin only - assigns editor to an article
     * @param int $article  article id
     * @param int $editor   editor id
     * @return ModelError|null  null if the operation was succesful, otherwise ModelError
     */
    public function addEditor(int $article, int $editor): null | ModelError {
        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN->value)
            return ModelError::NO_ACCESS;

        $user = $this->userModel->getUser($editor);
        if (!$user instanceof User) return ModelError::BAD_ID;
        if ($user->role != Role::EDITOR) return ModelError::BAD_ID;

        $reviewModel = new ReviewModel();
        $art = $this->getArticle($article);
        if (!$art instanceof Article) return ModelError::BAD_ID;
        $reviews = $reviewModel->getReviews([$art]);

        foreach ($reviews[$art->id] as $review) {
            if ($review->editor->id == $editor) return ModelError::REVIEW_EXISTS;
        }

        $sql = "INSERT INTO reviews (article, editor) VALUES (:article, :editor)";
        $this->db->fetchSQL($sql, ['article' => $article, 'editor' => $editor]);

        return null;
    }

    /**
     * Admin only - accepts/denies an article
     * @param int $article  article id
     * @param string $accept    true = accept, false = deny
     * @return ModelError|null  null if the operation was succesful, otherwise ModelError
     */
    public function acceptArticle(int $article, string $accept): null | ModelError {
        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN->value)
            return ModelError::NO_ACCESS;

        $art = $this->getArticle($article);
        if (!$art instanceof Article) return ModelError::BAD_ID;

        $reviewModel = new ReviewModel();
        $reviews = $reviewModel->getReviews([$art]);
        if (sizeof($reviews[$article]) < 3) return ModelError::NO_REVIEWS;

        $sql = "UPDATE articles SET status = :status WHERE id = :id";
        $this->db->fetchSQL($sql, ['id' => $article, 'status' => ($accept == 'true') ? 'accepted' : 'denied']);

        return null;
    }
}

class Article {
    public int $id;
    public string $title;
    public DateTime $date;
    public string $abstract;
    public string $file;
    public string $status;
    public User $author;

    function __construct(array $data, UserModel $userModel) {
        $this->id = $data["id"];
        $this->title = $data['title'];
        $this->date = new DateTime($data['date']);
        $this->abstract = $data['abstract'];
        $this->file = $data['file'];
        $this->status = $data['status'];
        $this->author = $userModel->getUser($data['author']);
    }
}