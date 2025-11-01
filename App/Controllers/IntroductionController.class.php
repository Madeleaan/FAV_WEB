<?php

namespace App\Controllers;

use App\Models\DatabaseModel;

/**
 * Controller, ktery vypise uvodni stranku
 */
class IntroductionController implements Icontroller {
    /** Sprava databaze */
    private DatabaseModel $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    /**
     * @param string $pageTitle Jmeno stranky
     * @return array    Vypis v sablone
     */
    public function show(string $pageTitle): array
    {
        return [
            "title" => $pageTitle,
            "stories" => $this->db->getAllIntroductions()
        ];
    }
}
