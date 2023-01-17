<?php
// Необходимые глобальные переменные для работы REST API

return [

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