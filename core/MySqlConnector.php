<?php

namespace Mns\Buggy\Core;

class MySqlConnector {
    private static $instance = null;
    private $pdo;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        $this->connect();
    }

    // Méthode pour obtenir l'instance unique de MySqlConnector
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        $host = $_ENV['DB_HOST'] ?? null;
        $port = $_ENV['DB_PORT'] ?? null;
        $dbname = $_ENV['DB_DATABASE'] ?? null;
        $username = $_ENV['DB_USERNAME'] ?? null;
        $password = $_ENV['DB_PASSWORD'] ?? null;

        if (!$host || !$port || !$dbname || !$username) {
            throw new \Exception("Variables d'environnement de base de données manquantes. Vérifiez votre fichier .env");
        }

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        try {
            $this->pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);

        } catch (\PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getServerConnection() {
        $host = $_ENV['DB_HOST'] ?? null;
        $port = $_ENV['DB_PORT'] ?? null;
        $username = $_ENV['DB_USERNAME'] ?? null;
        $password = $_ENV['DB_PASSWORD'] ?? null;

        if (!$host || !$port || !$username) {
            throw new \Exception("Variables d'environnement de base de données manquantes. Vérifiez votre fichier .env");
        }

        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";

        try {
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
            return $pdo;
        } catch (\PDOException $e) {
            throw new \Exception("Connection au serveur MySQL échouée : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}