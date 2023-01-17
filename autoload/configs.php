<?php
$pathConfig = $_SERVER['DOCUMENT_ROOT'].'/config';
$files = scandir($pathConfig);
$_app_configs = [];

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $_app_configs[] = include $pathConfig.'/'.$file;
    }
}

function __config(string $parse) {
    return $parse;
}