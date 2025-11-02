<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuthModel;
use App\Models\User;

/**
 * Controller, ktery vypise uvodni stranku
 */
class AdminUsersController implements IController {
    private AuthModel $auth;
    private UserModel $userModel;

    public function __construct() {
        $this->auth = new AuthModel();
        $this->userModel = new UserModel();
    }

    /**
     * @param string $pageTitle Jmeno stranky
     * @return array    Vypis v sablone
     */
    public function show(string $pageTitle): array
    {
        $logged = $this->auth->getLoggedUser();
        $users = [];
        if ($logged instanceof User) {
            $users = $this->userModel->getAllUsers();
        }


        return [
            "title" => $pageTitle,
            "logged" => $logged,
            "users" => $users
        ];
    }
}
