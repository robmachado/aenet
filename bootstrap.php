<?php

include __DIR__ . '/vendor/autoload.php';

$dir = __DIR__;
try {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
 } catch (Exception $e) {
    throw new \RuntimeException('Could not find a .env file.');
 }