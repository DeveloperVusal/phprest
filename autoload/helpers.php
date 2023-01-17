<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

function env($name) {
    return  $_ENV[$name];
}

function storage_path($path = null) {
    return $_SERVER['DOCUMENT_ROOT'].'/storage/'.$path;
}