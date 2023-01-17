<?php
namespace App\Controllers;

use App\Services\UserService;

class RegistrationController {
	public $result;

	public function __construct() {
		$service = new UserService();
		$this->result = $service->registration();
	}
}

// email=dev@dev.ru&password=12345&confirm_password=123456&name=Vusal&surname=Mamedov