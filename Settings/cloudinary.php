<?php
require __DIR__ . '/../vendor/autoload.php';
use Cloudinary\Configuration\Configuration;


Configuration::instance([
  'cloud' => [
    'cloud_name' => getenv('CLOUD_NAME'),
    'api_key' => getenv('API_KEY'),
    'api_secret' => getenv('API_SECRET')
  ],
  'url' => [
    'secure' => true
  ]
]);