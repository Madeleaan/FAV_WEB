<?php
session_start([
    'cookie_httponly' => true,
]);
include ('api/error.php');

class API {
    private PDO $pdo;

    function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "web_sem";

        $pdo = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo = $pdo;
    }

    function fetchSQL(string $sql, array $params, bool $all = false): array | null {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() == 0) return null;
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($all) return $res;
        else if ($res == []) return [];
        else return $res[0];
    }

    function createUser(string $login, string $pass, string $name): null | ApiError {
        if ($this->getUser($login) instanceof User) return new ApiError(ApiErrorList::LOGIN_EXISTS);

        $sql = "INSERT INTO users (login, password, name) VALUES (:login, :pass, :name)";
        $params = ["login" => $login, "pass" => password_hash($pass, PASSWORD_DEFAULT), "name" => $name];
        $this->fetchSQL($sql, $params);
        return null;
    }

    function loginUser(string $login, string $pass): null | ApiError {
        $user = $this->getUser($login);
        if ($user instanceof ApiError) return $user;

        if (!$user->enabled) return new ApiError(ApiErrorList::DISABLED_USER);
        if (password_verify($pass, $user->password)) {
            $_SESSION['login'] = $user->login;
            return null;
        } else {
            return new ApiError(ApiErrorList::BAD_PASS);
        }
    }

    function getUser(string $login): User | ApiError {
        $sql = "SELECT * FROM users WHERE login = :login";
        $data = $this->fetchSQL($sql, ["login" => $login]);

        if ($data == null) return new ApiError(ApiErrorList::BAD_LOGIN);
        else {
            return new User($data);
        }
    }

    function getUserFromId(int $id): User | ApiError {
        $sql = "SELECT * FROM users WHERE id = :id";
        $data = $this->fetchSQL($sql, ["id" => $id]);

        if ($data == null) return new ApiError(ApiErrorList::BAD_LOGIN);
        else {
            return new User($data);
        }
    }

    function toggleUser(string $login): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $user = $this->getUser($login);
        if ($user instanceof ApiError) return new ApiError(ApiErrorList::BAD_LOGIN);
        if ($this->currentUser()->role->value <= $user->role->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "UPDATE users SET enabled = :enabled WHERE login = :login";
        $this->fetchSQL($sql, ["enabled" => $user->enabled ? 0 : 1, "login" => $login]);

        return null;
    }

    function deleteUser(string $login): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $user = $this->getUser($login);
        if ($user instanceof ApiError) return new ApiError(ApiErrorList::BAD_LOGIN);
        if ($this->currentUser()->role->value <= $user->role->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "DELETE FROM users WHERE login = :login";
        $this->fetchSQL($sql, ["login" => $login]);

        return null;
    }

    function changeRole(string $login, int $role): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $user = $this->getUser($login);
        if ($user instanceof ApiError) return new ApiError(ApiErrorList::BAD_LOGIN);
        if ($this->currentUser()->role->value <= $user->role->value) return new ApiError(ApiErrorList::NO_ACCESS);
        if ($this->currentUser()->role->value <= $role) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "UPDATE users SET role = :role WHERE login = :login";
        $this->fetchSQL($sql, ["role" => $role, "login" => $login]);

        return null;
    }

    function listUsers(): array | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "SELECT * FROM users";
        $data = $this->fetchSQL($sql, [], true);

        $res = [];
        foreach ($data as $user) $res[] = new User($user);
        return $res;
    }

    function currentUser(): User | null {
        if (empty($_SESSION['login'])) return null;

        $user = $this->getUser($_SESSION['login']);
        if ($user instanceof ApiError) return null;
        return $user;
    }

    function getUserArticles(string $login): array | ApiError {
        if ($login == null) return new ApiError(ApiErrorList::BAD_LOGIN);
        if ($this->getUser($login) instanceof ApiError) return new ApiError(ApiErrorList::BAD_LOGIN);

        $sql = "SELECT a.* FROM articles a
            JOIN users u ON u.id = a.author WHERE login = :login
            ORDER BY a.date DESC";
        $data = $this->fetchSQL($sql, ['login' => $login], true);

        $res = [];
        foreach ($data as $article) $res[] = new Article($article);
        return $res;
    }

    function deleteArticle(int $id): null | ApiError {
        if ($id == null) return new ApiError(ApiErrorList::MISSING_PARAMS);

        $sql = "SELECT login, status, file FROM articles a 
                    JOIN users u ON u.id = a.author WHERE a.id = :id";
        $data = $this->fetchSQL($sql, ["id" => $id]);
        if ($data == null || $data['login'] != $this->currentUser()->login) return new ApiError(ApiErrorList::NO_ACCESS);
        if ($data['status'] == 'accepted') return new ApiError(ApiErrorList::ARTICLE_PUBLIC);

        $sql = "DELETE FROM articles WHERE id = :id";
        $this->fetchSQL($sql, ["id" => $id]);

        $dir = "../storage/articles/";
        unlink($dir . $data['file']);

        return null;
    }

    function updateArticle(int $id, string $title, string $abstract): null | ApiError {
        if ($id == null) return new ApiError(ApiErrorList::MISSING_PARAMS);

        $sql = "SELECT login, status FROM articles a
                    JOIN users u ON u.id = a.author WHERE a.id = :id";
        $data = $this->fetchSQL($sql, ["id" => $id]);
        if ($data == null || $data['login'] != $this->currentUser()->login) return new ApiError(ApiErrorList::NO_ACCESS);
        if ($data['status'] == 'accepted') return new ApiError(ApiErrorList::ARTICLE_PUBLIC);

        $sql = "UPDATE articles SET title = :title, abstract = :abstract WHERE id = :id";
        $params = ["title" => $title, "abstract" => $abstract, "id" => $id];
        $this->fetchSQL($sql, $params);

        return null;
    }

    function postArticle(string $title, string $abstract, string $file): null | ApiError {
        if ($title == null || $abstract == null || $file == null) return new ApiError(ApiErrorList::MISSING_PARAMS);
        if ($this->currentUser() == null) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "INSERT INTO articles (title, abstract, file, author) values (:title, :abstract, :file, :author)";
        $params = [
            "title" => $title,
            "abstract" => $abstract,
            "file" => $file,
            "author" => $this->currentUser()->id
        ];
        $this->fetchSQL($sql, $params);

        return null;
    }

    function listArticles(): array | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN->value)
            return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "SELECT * FROM articles ORDER BY date DESC";
        $data =  $this->fetchSQL($sql, [], true);

        $res = [];
        foreach ($data as $article) $res[] = new Article($article);
        return $res;
    }

    function listAvailableEditors(int $article): array | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN)
            return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "SELECT * FROM users WHERE role = 2 AND id NOT IN (SELECT editor from reviews WHERE article = :article) AND enabled = 1";
        $data = $this->fetchSQL($sql, ["article" => $article], true);

        $res = [];
        if ($data != null) {
            foreach ($data as $user) $res[] = new User($user);
        }
        return $res;
    }

    function getReviews(int $article): array | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN)
            return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "SELECT * FROM reviews WHERE article = :article";
        $data = $this->fetchSQL($sql, ["article" => $article], true);

        $res = [];
        if ($data != null) {
            foreach ($data as $review) $res[] = new Review($review);
        }
        return $res;
    }

    function addEditor(int $article, int $editor): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN)
            return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "SELECT * FROM users WHERE role = 2 AND id = :editor AND enabled = 1";
        $data = $this->fetchSQL($sql, ['editor' => $editor]);
        if ($data == null) return new ApiError(ApiErrorList::BAD_LOGIN);

        $sql = "SELECT * FROM reviews WHERE article = :article AND editor = :editor";
        $data = $this->fetchSQL($sql, ["article" => $article, "editor" => $editor]);
        if ($data != null) return new ApiError(ApiErrorList::REVIEW_EXISTS);

        $sql = "INSERT INTO reviews (article, editor) VALUES (:article, :editor)";
        $this->fetchSQL($sql, ['article' => $article, 'editor' => $editor]);

        return null;
    }

    function deleteReview(int $review): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN)
            return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "DELETE FROM reviews WHERE id = :id";
        $this->fetchSQL($sql, ["id" => $review]);

        return null;
    }

    function acceptArticle(int $article, string $accept): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < Role::ADMIN)
            return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "SELECT id FROM reviews WHERE article = :id";
        $data = $this->fetchSQL($sql, ["id" => $article], true);

        if (sizeof($data) < 3) return new ApiError(ApiErrorList::NO_REVIEWS);

        $sql = "UPDATE articles SET status = :status WHERE id = :id";
        $this->fetchSQL($sql, ["id" => $article, "status" => ($accept == 'true') ? 'accepted' : 'denied']);

        return null;
    }
}

enum Role: int
{
    case UNKNOWN = 0;
    case AUTHOR = 1;
    case EDITOR = 2;
    case ADMIN = 3;
    case SUPERADMIN = 4;

    public static function str(Role $r): string {
        return match ($r) {
            Role::UNKNOWN => "Unknown role",
            Role::AUTHOR => "Autor",
            Role::EDITOR => "Editor",
            Role::ADMIN => "Admin",
            Role::SUPERADMIN => "Superadmin",
        };
    }
}

class User {
    public string $login;
    public string $password;
    public string $name;
    public Role $role;
    public bool $enabled;
    public int $id;

    function __construct($data)
    {
        $this->login = $data['login'];
        $this->password = $data['password'];
        $this->name = $data['name'];
        $this->role = Role::from(intval($data['role']));
        $this->enabled = boolval($data['enabled']);
        $this->id = intval($data['id']);
    }
}

class Article {
    public int $id;
    public string $title;
    public DateTime $date;
    public string $abstract;
    public string $file;
    public bool $public;
    public string $status;

    function __construct($data) {
        $this->id = $data["id"];
        $this->title = $data['title'];
        $this->date = new DateTime($data['date']);
        $this->abstract = $data['abstract'];
        $this->file = $data['file'];
        $this->public = boolval($data['public']);
        $this->status = $data['status'];
    }
}

class Review {
    public int $id;
    public int $article;
    public int $editor;
    public float $quality;
    public float $language;
    public float $relevancy;

    function __construct($data) {
        $this->id = $data["id"];
        $this->article = $data["article"];
        $this->editor = $data["editor"];
        $this->quality = $data["quality"];
        $this->language = $data["language"];
        $this->relevancy = $data["relevancy"];
    }
}

/* handle deleting/disabling currently logged in user */
if (!empty($_SESSION['login'])) {
    $api = new API();
    $user = $api->getUser($_SESSION['login']);
    if ($user instanceof ApiError || !$user->enabled) {
        session_destroy();
        header("Refresh: 0");
    }
}