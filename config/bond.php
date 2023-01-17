<?php

return [
    // Ключ для подписи JWT
	'jwt_secret' => env('JWT_SECRET_KEY'),

	// Данные для проверки приложения с БД
	'connections' => [
		'user_id' => env('APP_USER_ID'),
		'secret_key' => env('APP_SECRET_KEY'),
		'domain' => env('APP_DOMAIN')
	],
];