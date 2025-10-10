<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "web_sem";
    global $pdo;

    header("Content-Type: application/json");

    try {
        $pdo = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: ".$e->getMessage());
    }

    function error($code, $message, $err) {
        http_response_code($code);
        echo json_encode(["msg" => $message, "error" => $err]);
        die();
    }