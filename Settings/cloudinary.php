<?php
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Cloudinary\Configuration\Configuration;

//Cargo las variables del .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

//Ahora, las configuro
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
    ],
    'url' => [
        'secure' => true
    ]
]);
