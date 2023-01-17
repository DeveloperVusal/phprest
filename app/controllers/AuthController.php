<?php
namespace App\Controllers;

use App\Services\UserService;

class AuthController {
	public $result;

	public function __construct(array $data = null) {
		$service = new UserService();
		$this->result = $service->auth();
	}

	public static function auth_verify($token)
	{
		$service = new UserService();
		
		return $service->auth_verify($token);
	}

	public static function logout($token)
	{
		$service = new UserService();
		
		return $service->logout($token);
	}

	public static function forgot_password($email)
	{
		$service = new UserService();
		
		return $service->forgot_password($email);
	}

	public static function confirm_code($userId, $code, $activation = false)
	{
		$service = new UserService();
		
		return $service->confirm_code($userId, $code, $activation);
	}
}

// email=dev@dev.ru&password=12345&confirm_password=123456&name=Vusal&surname=Mamedov