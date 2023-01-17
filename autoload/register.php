<?php
$cfg_apps = include $_SERVER['DOCUMENT_ROOT'].'/config/apps.php';

foreach ($cfg_apps as $key => $val) {
	$GLOBALS['APPCFG_'.$key] = $val;
}

$cfg_apipaths = include $_SERVER['DOCUMENT_ROOT'].'/config/api-paths.php';

foreach ($cfg_apipaths as $key => $val) {
	$GLOBALS[$key] = $val;
}

function app_autoloader($className) {
	$defaultClassName = $className;
	$className = str_replace('\\', '/', $className);
	$dirPath = str_replace('\\', '/', dirname(__FILE__));

	$expl = explode('/', $className);
	$explEnd = end($expl);

	unset($expl[sizeof($expl) - 1]);

	$className = implode('/', array_map(function($val) {
		return strtolower($val);
	}, $expl)).'/'.$explEnd;

	include $dirPath.'/../'.$className.'.php';

	$reflectionClass = new ReflectionClass($defaultClassName);

	if ($reflectionClass->isAbstract() === false) {
		if (method_exists($defaultClassName, '__constructStatic')) $defaultClassName::__constructStatic();
	}
}

spl_autoload_register('app_autoloader');