<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

function env($name) {
    return  $_ENV[$name];
}

function storage_path($path = null) {
    $storage = __DIR__.'/../storage/';

    if (!is_dir($storage)) mkdir($storage, 0775);

    return $storage.$path;
}