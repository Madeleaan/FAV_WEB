<?php

namespace App\Models;

use PDO;

/**
 * Trida spravujici spojeni s databazi
 */
class DatabaseModel
{
    /** PDO pro komunikaci s databazi */
    private PDO $pdo;

    /**
     *  Inicializace spojeni
     */
    public function __construct()
    {
        $this->pdo = new PDO(
            "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $this->pdo->exec("set names utf8");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllIntroductions(): array {
        return [
            ["id_introduction" => 1, "date" => "2016-11-01 10:53:00", "author" => "A.B.", "title" => "Nadpis", "text" => "abcd"]
        ];
    }
}
