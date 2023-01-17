<?php
header('Content-Type: application/json');

if ($_SERVER['HTTP_REFERER']) {
    $domainPath = parse_url($_SERVER['HTTP_REFERER']);
    $domainPath = $domainPath['scheme'].'://'.$domainPath['host'];
} else {
    $domainPath = 'http://accounts.butago.com';
}

header('Access-Control-Allow-Origin: '.$domainPath);
header('Access-Control-Allow-Headers: Authorization, X-Requested-With, Content-Type, crossDomain, Accept');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Credentials: true');

// ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

require('./vendor/autoload.php');
require('./autoload/helpers.php');
require('./autoload/register.php');

use Http\Router;

include './routes/api.php';

Router::getExecute($_SERVER['REQUEST_URI']);