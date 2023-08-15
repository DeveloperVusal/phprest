<?php
use Core\Facades\Http\Route;
use Core\Http\Request;

Route::get('/', function() {
	echo 'This is «PHPRest» framework!';
});

Route::get('/param', function(Request $req) {
	echo '<pre>';
	print_r($req->user_agent());
	echo '</pre>';
});