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
			'r_pgsql_accounts' => [
				'host' => env('DB_HOST_R'),
				'port' => env('DB_PORT_R'),
				'username' => env('DB_USERNAME_R'),
				'password' => env('DB_PASSWORD_R'),
				'dbname' => env('DB_DATABASE_R'),
				'charset' => 'utf8',
				'schema' => 'public'
			],
			'w_pgsql_accounts' => [
				'host' => env('DB_HOST_W'),
				'port' => env('DB_PORT_W'),
				'username' => env('DB_USERNAME_W'),
				'password' => env('DB_PASSWORD_W'),
				'dbname' => env('DB_DATABASE_W'),
				'charset' => 'utf8',
				'schema' => 'public'
			],
		]
	]
];