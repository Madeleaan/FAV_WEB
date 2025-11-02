<?php

namespace App\Models;

class UserModel {
    private DatabaseModel $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    /**
     * Gets the user object from specified id
     * @param int $id   id of the user
     * @return User|null    User if a user with specified id is found, null otherwise
     */
    public function getUser(int $id): User | null {
        $sql = "SELECT * FROM users WHERE id = :id";
        $data = $this->db->fetchSQL($sql, ["id" => $id]);

        if ($data == null) return null;
        else {
            return new User($data);
        }
    }

    /**
     * Gets the user object from specified login
     * @param string $login   login of the user
     * @return User|null    User if a user with specified login is found, null otherwise
     */
    public function getUserByLogin(string $login): User | null {
        $sql = "SELECT * FROM users WHERE login = :login";
        $data = $this->db->fetchSQL($sql, ["login" => $login]);

        if ($data == null) return null;
        else {
            return new User($data);
        }
    }

    /**
     * Admin only - gets all users
     * @return User[]|ModelError
     */
    public function getAllUsers(): array | ModelError {
        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN->value)
            return ModelError::NO_ACCESS;

        $sql = "SELECT * FROM users";
        $data = $this->db->fetchAllSQL($sql, []);

        $res = [];
        foreach ($data as $user) $res[] = new User($user);
        return $res;
    }

    /**
     * Admin only - toggle a user
     * @param string $login   login of the user
     * @return ModelError|null  null if the operation was succesful, otherwise ModelError
     */
    public function toggleUser(string $login): null | ModelError {
        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN) {
            return ModelError::NO_ACCESS;
        }

        $user = $this->getUserByLogin($login);
        if ($user == null) return ModelError::BAD_LOGIN;
        if ($user->role->value >= $logged->role->value) return ModelError::NO_ACCESS;

        $sql = "UPDATE users SET enabled = :enabled WHERE login = :login";
        $this->db->fetchSQL($sql, ["login" => $login, "enabled" => $user->enabled ? 0 : 1]);

        return null;
    }

    /**
     * Admin only - delete a user
     * @param string $login login of the user
     * @return ModelError|null  null if the operation was succesful, otherwise ModelError
     */
    public function deleteUser(string $login): null | ModelError {
        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN) {
            return ModelError::NO_ACCESS;
        }

        $user = $this->getUserByLogin($login);
        if ($user == null) return ModelError::BAD_LOGIN;
        if ($user->role->value >= $logged->role->value) return ModelError::NO_ACCESS;

        $sql = "DELETE FROM users WHERE login = :login";
        $this->db->fetchSQL($sql, ["login" => $login]);

        return null;
    }

    /**
     * Admin only - change role of a user
     * @param string $login login of the user
     * @param int $role role to change the user to
     * @return ModelError|null  null if the operation was succesful, otherwise ModelError
     */
    public function changeRole(string $login, int $role): null | ModelError {
        if ($login == "" || Role::tryFrom($role) == null) return ModelError::MISSING_PARAMS;

        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN) {
            return ModelError::NO_ACCESS;
        }

        $user = $this->getUserByLogin($login);
        if ($user == null) return ModelError::BAD_LOGIN;
        if ($user->role->value >= $logged->role->value) return ModelError::NO_ACCESS;
        if ($logged->role->value <= $role) return ModelError::NO_ACCESS;

        $sql = "UPDATE users SET role = :role WHERE login = :login";
        $this->db->fetchSQL($sql, ["role" => $role, "login" => $login]);

        return null;
    }

    /**
     * Admin only - Gets available editors for an array of articles
     * @param Article[] $articles   array of articles
     * @return array|ModelError array of article ids that contain array of users
     */
    public function getAvailableEditors(array $articles): array | ModelError {
        if (!$articles) return ModelError::MISSING_PARAMS;

        $auth = new AuthModel();
        $logged = $auth->getLoggedUser();
        if (!$logged instanceof User || $logged->role->value < Role::ADMIN) {
            return ModelError::NO_ACCESS;
        }

        $res = [];
        $sql = "SELECT * FROM users WHERE role = " . Role::EDITOR->value . " AND id NOT IN 
            (SELECT editor FROM reviews WHERE article = :article) AND enabled = 1";

        foreach ($articles as $article) {
            $data = $this->db->fetchAllSQL($sql, ["article" => $article->id]);

            if ($data != null) {
                $editors = [];
                foreach ($data as $editor) {
                    $editors[] = new User($editor);
                }
                $res[$article->id] = $editors;
            }
        }

        return $res;
    }
}

class User {
    /** Uzivatelske jmeno */
    public string $login;
    /** Enkryptovane heslo */
    public string $password;
    /** Jmeno */
    public string $name;
    /** Role */
    public Role $role;
    /** Je uzivatel povolen? */
    public bool $enabled;
    /** ID v databazi */
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

enum Role: int
{
    case UNKNOWN = 0;
    case AUTHOR = 1;
    case EDITOR = 2;
    case ADMIN = 3;
    case SUPERADMIN = 4;

    /**
     * Textova reprezentace role
     * @return string
     */
    public function str(): string {
        return match ($this) {
            Role::UNKNOWN => "Unknown role",
            Role::AUTHOR => "Autor",
            Role::EDITOR => "Editor",
            Role::ADMIN => "Admin",
            Role::SUPERADMIN => "Superadmin",
        };
    }
}