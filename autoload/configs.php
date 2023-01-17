<?php
$pathConfig = __DIR__.'/../config';
$files = scandir($pathConfig);
$_app_configs = [];

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $expName = explode('.', $file);
        $expName = implode('', array_slice($expName, 0, sizeof($expName) - 1));
        $_app_configs[$expName] = include $pathConfig.'/'.$file;
    }
}

function config(string $parse) {
    global $_app_configs;

    $arrEval = explode('/', $parse);
    $outEval = '$outEval = $_app_configs[\''.$arrEval[0].'\']';

    array_map(function($val) use(&$outEval) {
        $outEval .= '[\''.$val.'\']';

        return $val;
    }, array_slice($arrEval, 1, sizeof($arrEval)));

    $outEval .= ';';

    eval($outEval);

    return $outEval;
}

$GLOBALS['APP_ROUTES'] = [];
$GLOBALS['APP_ROUTES_STORE'] = [];