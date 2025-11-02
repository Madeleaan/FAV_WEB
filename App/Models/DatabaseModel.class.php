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
            "mysql:host=" . "localhost" . ";dbname=" . "web_sem",
            "root",
            ""
        );
        $this->pdo->exec("set names utf8");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function fetchSQL(string $sql, array $params): array | null {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() == 0) return null;
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) return $res;
        else return null;
    }

    public function fetchAllSQL(string $sql, array $params): array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() == 0) return [];
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($res) return $res;
        else return [];
    }
}
