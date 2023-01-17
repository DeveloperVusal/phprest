<?php
namespace App\Controllers;

use Http\Request;

use App\Services\UserService;
use App\Services\SxGeo\SxGeoService;
use App\Services\LanguageService;

class UserController {
	/**
	 * Метод обновляет пароль пользователя
	 * 
	 * @param integer $userId
	 * @param array $data
	 * @static
	 * @return string|boolean json string
	 */
	public static function update_password($userId, $data)
	{
		$service = new UserService();
		
		return $service->update_password($userId, $data);
	}

	/**
	 * Метод получения данных пользователя
	 * 
	 * @param string $token
	 * @static
	 * @return string|boolean json string
	 */
	public static function get_data($token)
	{
		$service = new UserService();
		
		return $service->get_data($token);
	}

	/**
	 * Метод определения местоположения пользователя
	 * 
	 * 
	 */
	public static function locale(Request $request) {
		// Определение местоположения
		$sxgeo = new SxGeoService();
		$geoData = $sxgeo->get($request->field('user_ip') ?? $request->ip());
		
		if ($geoData) {
			$countryCode = strtoupper($geoData['country']['iso']);
			$geoCity = $geoData['city']['name_en'];
		}
	
		if (empty($countryCode)) $countryCode = 'AZ';
		if (empty($geoCity)) $geoCity = 'Baku';
		
		return [
			'country' => $countryCode,
			'city' => $geoCity
		];
	}
}

// email=dev@dev.ru&password=12345&confirm_password=123456&name=Vusal&surname=Mamedov