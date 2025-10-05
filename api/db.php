<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "web_sem";

    try {
        $pdo = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    function error($code, $message) {
        http_response_code($code);
        echo json_encode(array("error" => $message));
        die();
    }