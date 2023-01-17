<?php
namespace App\Controllers;

use App\Services\UserService;

class AuthRefreshTokenController {
	/**
	 * Метод контроллера обновления пары токенов
	 * 
	 * @param string $token
	 * @param string $user_agent
	 * @static
	 * @return string JSON объект
	 */
	public static function refresh($token, $user_agent)
	{
		$service = new UserService();
		
		return $service->refresh_token($token, $user_agent);
	}
}