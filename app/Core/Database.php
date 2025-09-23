<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;

    public static function connect() {
        if (!self::$instance) {
            // Tenta carregar do .env (para desenvolvimento)
            try {
            $dbHost = $_ENV['DB_HOST'] ;
            $dbName = $_ENV['DB_NAME'] ;
            $dbUser = $_ENV['DB_USER'] ;
            $dbPass = $_ENV['DB_PASS'] ;
            $dbPort = $_ENV['DB_PORT'] ;

            // $dbHost = "dpg-d347adre5dus73eph6v0-a" ;
            // $dbName = "default_scd3" ;
            // $dbUser = "default_scd3_user";
            // $dbPass = "eyO6wYUv5vQMU2l6BJ2etaZlqU7yvSBx";
            // $dbPort = 5432 ;
            } catch (\Exception $e) {
                // Ignora erro se .env não existir (produção)
            }


            // CORREÇÃO AQUI: Use pgsql em vez de mysql
            $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
            
            try {
                self::$instance = new PDO($dsn, $dbUser, $dbPass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("DB Error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}