<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer install" to load the Symfony application.');
}

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
