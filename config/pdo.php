<?php

Class MyPDO{
    public static function connect(){
        // Env vars let Docker point at its own DB host/creds; defaults preserve the original local setup.
        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'shop';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '0000';

        try{
            $pdo = new PDO("mysql:host={$host};dbname={$name};charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }catch(PDOException $e){
            error_log('DB connection failed: ' . $e->getMessage());
            http_response_code(500);
            die('Service temporarily unavailable.');
        }
    }
}