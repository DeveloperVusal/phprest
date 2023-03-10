<?php
require('./vendor/autoload.php');
require('./autoload/helpers.php');

header('Content-Type: '.$_ENV['APP_CONTENT_TYPE']);

if (isset($_SERVER['HTTP_REFERER']) && mb_strlen($_SERVER['HTTP_REFERER'])) {
    $domainPath = parse_url($_SERVER['HTTP_REFERER']);
    $domainPath = $domainPath['scheme'].'://'.$domainPath['host'].((isset($domainPath['port']) && $domainPath['port']) ? ':'.$domainPath['port'] : '');
} else {
    $domainPath = $_ENV['APP_REFERER_URI'];
}

header('Access-Control-Allow-Origin: '.$domainPath);
header('Access-Control-Allow-Headers: Authorization, X-Requested-With, Content-Type, crossDomain, Accept');
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Credentials: true');

require('./autoload/configs.php');
require('./autoload/register.php');

use Core\Facades\Http\Router;

include './routes/api.php';

Router::getExecute($_SERVER['REQUEST_URI']);