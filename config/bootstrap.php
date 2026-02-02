<?php

session_start();

// Load routes from config/routes.json
$routesFile = __DIR__ . '/routes.json';
$routes = file_exists($routesFile) ? json_decode(file_get_contents($routesFile), true) : [];

// Détecter le chemin absolu du projet
$projectDir = realpath(__DIR__ . '/../');

// Vérifier si un fichier .env.local existe et charger
$envFileLocal = $projectDir . '/.env.local';
$envFile = file_exists($envFileLocal) ? '.env.local' : '.env';

// Vérifier que le fichier .env existe
if (!file_exists($projectDir . '/' . $envFile)) {
    throw new \Exception("Le fichier .env n'a pas été trouvé dans : " . $projectDir);
}

// Charger Dotenv
$dotenv = Dotenv\Dotenv::createImmutable($projectDir, $envFile);
$dotenv->load();
