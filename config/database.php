<?php

return [
	'connections' => [
		'mysql' => [
			'mysql' => [
				'host' => env('DB_HOST'),
				'port' => env('DB_PORT'),
				'username' => env('DB_USERNAME'),
				'password' => env('DB_PASSWORD'),
				'dbname' => env('DB_DATABASE')
			],
		],
		'postgresql' => [
			'pgsql' => [
				'host' => env('DB_HOST'),
				'port' => env('DB_PORT'),
				'username' => env('DB_USERNAME'),
				'password' => env('DB_PASSWORD'),
				'dbname' => env('DB_DATABASE'),
				'charset' => 'utf8',
				'schema' => 'public'
			],
		]
	]
];