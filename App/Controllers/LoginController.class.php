<?php

namespace App\Controllers;

use App\Models\AuthModel;

/**
 * Controller, ktery vypise uvodni stranku
 */
class LoginController implements IController {
    private AuthModel $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    /**
     * @param string $pageTitle Jmeno stranky
     * @return array    Vypis v sablone
     */
    public function show(string $pageTitle): array
    {
        if ($this->authModel->getLoggedUser() != null) header("Location: /");

        return [
            "title" => $pageTitle
        ];
    }
}
