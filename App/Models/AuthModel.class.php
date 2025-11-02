<?php

namespace App\Models;

class AuthModel {
    private DatabaseModel $db;
    private UserModel $userModel;
    private SessionModel $session;

    public function __construct() {
        $this->db = new DatabaseModel();
        $this->userModel = new UserModel();
        $this->session = new SessionModel();

        /* handle deleting/disabling currently logged in user */
        $logged = $this->getLoggedUser();
        if ($logged == ModelError::BAD_LOGIN || ($logged != null && !$logged->enabled)) {
            $this->logoutUser();
            header('Refresh: 0');
        }
    }

    /**
     * Gets the currently logged user
     * @return User|ModelError|null User if a user is logged, null otherwise. ApiErrorList if the user does not exist
     */
    public function getLoggedUser(): User | ModelError | null {
        $session = new SessionModel();
        if (!$session->isSessionSet('user')) return null;

        $id = $session->readSession('user');
        $user = $this->userModel->getUser($id);
        if ($user == null) {
            $session->removeSession('user');
            return ModelError::BAD_LOGIN;
        }

        return $user;
    }

    /**
     * Logs a user with the specified credentials
     * @param string $login Login of the user
     * @param string $pass  Password of the user in plaintext
     * @return null|ModelError null if a login is succesful, ApiErrorList otherwise
     */
    public function loginUser(string $login, string $pass): null | ModelError {
        if ($login == "" || $pass == "") return ModelError::MISSING_PARAMS;

        $user = $this->userModel->getUserByLogin($login);
        if ($user == null) return ModelError::BAD_LOGIN;
        if (!$user->enabled) return ModelError::DISABLED_USER;

        if (password_verify($pass, $user->password)) {
            $this->session->setSession('user', $user->id);
            return null;
        } else {
            return ModelError::BAD_PASS;
        }
    }

    /**
     * Creates a new user in the database with author role
     * @param string $login Login of the user
     * @param string $pass  Password of the user in plaintext
     * @param string $name  Name of the user
     * @return null|ModelError null if creation is succesful, ModelError otherwise
     */
    public function createUser(string $login, string $pass, string $name): null | ModelError {
        if ($login == "" || $pass == "" || $name == "") return ModelError::MISSING_PARAMS;
        if ($this->userModel->getUserByLogin($login) instanceof User) return ModelError::LOGIN_EXISTS;

        $sql = "INSERT INTO users (login, password, name) VALUES (:login, :pass, :name)";
        $params = ["login" => $login, "pass" => password_hash($pass, PASSWORD_DEFAULT), "name" => $name];
        $this->db->fetchSQL($sql, $params);
        return null;
    }

    public function logoutUser(): void {
        $this->session->removeSession('user');
    }
}