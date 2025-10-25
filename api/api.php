<?php
session_start();
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

    function fetchSQL(string $sql, array $params): array | null {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() == 0) return null;
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) == 1) return $res[0];
        else return $res;
    }

    function createUser(string $login, string $pass, string $name): null | ApiError {
        if ($this->getUser($login) instanceof User) return new ApiError(ApiErrorList::LOGIN_EXISTS);

        $sql = "INSERT INTO users (login, password, name) VALUES (:login, :pass, :name)";
        $params = ["login" => $login, "pass" => password_hash($pass, PASSWORD_DEFAULT), "name" => $name];
        return $this->fetchSQL($sql, $params) != null ? null : new APIError(ApiErrorList::DB_ERROR);
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
        $params = ["login" => $login];
        $data = $this->fetchSQL($sql, $params);
        if ($data == null) return new ApiError(ApiErrorList::BAD_LOGIN);
        else {
            return new User($data);
        }
    }

    function toggleUser(string $login): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < 3) return new ApiError(ApiErrorList::NO_ACCESS);

        $user = $this->getUser($login);
        if ($user instanceof ApiError) return new ApiError(ApiErrorList::BAD_LOGIN);
        if ($this->currentUser()->role->value <= $user->role->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "UPDATE users SET enabled = :enabled WHERE login = :login";
        $params = ["enabled" => $user->enabled ? 0 : 1, "login" => $login];
        $this->fetchSQL($sql, $params);
        return null;
    }

    function deleteUser(string $login): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < 3) return new ApiError(ApiErrorList::NO_ACCESS);

        $user = $this->getUser($login);
        if ($user instanceof ApiError) return new ApiError(ApiErrorList::BAD_LOGIN);
        if ($this->currentUser()->role->value <= $user->role->value) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "DELETE FROM users WHERE login = :login";
        $params = ["login" => $login];
        $this->fetchSQL($sql, $params);
        return null;
    }

    function changeRole(string $login, int $role): null | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < 3) return new ApiError(ApiErrorList::NO_ACCESS);

        $user = $this->getUser($login);
        if ($user instanceof ApiError) return new ApiError(ApiErrorList::BAD_LOGIN);
        if ($this->currentUser()->role->value <= $user->role->value) return new ApiError(ApiErrorList::NO_ACCESS);
        if ($this->currentUser()->role->value <= $role) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "UPDATE users SET role = :role WHERE login = :login";
        $params = ["role" => $role, "login" => $login];
        $this->fetchSQL($sql, $params);
        return null;
    }

    function listUsers(): array | ApiError {
        if ($this->currentUser() == null || $this->currentUser()->role->value < 3) return new ApiError(ApiErrorList::NO_ACCESS);

        $sql = "SELECT * FROM users";
        $data = $this->fetchSQL($sql, []);
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
        $params = ['login' => $login];
        $data = $this->fetchSQL($sql, $params);
        $res = [];
        foreach ($data as $article) $res[] = new Article($article);
        return $res;
    }

    function deleteArticle(int $id): null | ApiError {
        if ($id == null) return new ApiError(ApiErrorList::MISSING_PARAMS);

        $sql = "SELECT login, public, file FROM articles a 
                    JOIN users u ON u.id = a.author WHERE a.id = :id";
        $params = ["id" => $id];
        $data = $this->fetchSQL($sql, $params);
        if ($data == null || $data['login'] != $this->currentUser()->login) return new ApiError(ApiErrorList::NO_ACCESS);
        if ($data['public']) return new ApiError(ApiErrorList::ARTICLE_PUBLIC);

        $sql = "DELETE FROM articles WHERE id = :id";
        $this->fetchSQL($sql, $params);

        $dir = "../storage/articles/";
        unlink($dir . $data['file']);

        return null;
    }

    function updateArticle(int $id, string $title, string $abstract): null | ApiError {
        if ($id == null) return new ApiError(ApiErrorList::MISSING_PARAMS);

        $sql = "SELECT login, public FROM articles a
                    JOIN users u ON u.id = a.author WHERE a.id = :id";
        $params = ["id" => $id];
        $data = $this->fetchSQL($sql, $params);
        if ($data == null || $data['login'] != $this->currentUser()->login) return new ApiError(ApiErrorList::NO_ACCESS);
        if ($data['public']) return new ApiError(ApiErrorList::ARTICLE_PUBLIC);

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

        $sql = "SELECT * FROM articles";
        $params = [];
        return $this->fetchSQL($sql, $params);
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

    function __construct($data) {
        $this->id = $data["id"];
        $this->title = $data['title'];
        $this->date = new DateTime($data['date']);
        $this->abstract = $data['abstract'];
        $this->file = $data['file'];
        $this->public = boolval($data['public']);
    }
}

/* handle deleting/disabling currently logged in user */
if (!empty($_SESSION['login'])) {
    $api = new API();
    $user = $api->getUser($_SESSION['login']);
    if ($user instanceof ApiError || !$user->enabled) {
        $_SESSION['login'] = null;
        header("Refresh: 0");
    }
}