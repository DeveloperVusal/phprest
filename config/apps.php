<?php
// Необходимые глобальные переменные для работы REST API

return [
	// Ключ для подписи JWT
	'jwt_secret' => env('JWT_SECRET_KEY'),

	// Данные для проверки приложения с БД
	'connections' => [
		'user_id' => env('APP_USER_ID'),
		'secret_key' => env('APP_SECRET_KEY'),
		'domain' => env('APP_DOMAIN')
	],

	// Версия API
	'versions' => [
		'v1'
	],

	// Типы и методы API
	'methods' => [
		// Тип Аккаунты
		// Методы авторизации и регистрации
		[
			'accounts' => [
				'auth', 'auth_verify', 'registration', 
				'refresh', 'logout', 'confirm_code',
				'forgot_password', 'locale'
			]
		],

		// Тип Пользователи
		// Методы получения, создания и обновления
		[
			'users' => [
				'get', 'create', 'update', 'delete'
			]
		]
	]
];