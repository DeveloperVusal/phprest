<?php
use Core\Http\Request;
use Core\Facades\Http\Route;

use App\Controllers\VerifyAppController;

use App\Controllers\RegistrationController;
use App\Controllers\AuthController;
use App\Controllers\AuthRefreshTokenController;
use App\Controllers\UserController;

// Метод регистрации пользователя
Route::post('/v1/accounts/registration', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$reg = new RegistrationController();

		echo $reg->result;
	} else {
		echo $verifyApp->message;
	}
});

// Метод авторизации пользователя
Route::post('/v1/accounts/auth', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$auth = new AuthController();

		echo $auth->result;
	} else {
		echo $verifyApp->message;
	}
});

// Метод получения нового acceess и refresh токена
Route::get('/v1/accounts/refresh', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();

		$authRefresh = AuthRefreshTokenController::refresh($request->cookie_http('butago.com:refresh_token'), $request->field('user_agent'));

		echo $authRefresh;
	} else {
		echo $verifyApp->message;
	}
});

// Метод аутентификации пользователи
Route::get('/v1/accounts/auth_verify', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();

		$token = $request->header('Authorization');
		$token = explode(' ', $token);

		if (AuthController::auth_verify($token[1])) {
			http_response_code(200);
		} else {
			http_response_code(401);
		}
	} else {
		echo $verifyApp->message;
	}
});

// Метод опеделения локали (языка) пользователя
Route::get('/v1/accounts/locale', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();
		$geo = UserController::locale($request);

		if (strlen($_COOKIE['user-locale'] >= 2)) $localeCode = $_COOKIE['user-locale'];
		else $localeCode = $geo['country'];

		echo json_encode([
			'localeCode' => strtoupper($localeCode), 
			'country' => $geo['country'],
			'city' => $geo['city']
		]);
	} else {
		echo $verifyApp->message;
	}
});

// Метод выхода (логаута) пользователя
Route::get('/v1/accounts/logout', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();

		if (AuthController::logout($request->cookie_http('butago.com:refresh_token'))) {
			http_response_code(200);
		} else {
			http_response_code(412);
		}
	} else {
		echo $verifyApp->message;
	}
});

// Метод подтверждения кода проверки
Route::post('/v1/accounts/confirm_code', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();
		$result = AuthController::confirm_code($request->field('userid'), $request->field('code'), (bool)$request->field('activation'));

		if ($result) {
			http_response_code(200);

			echo $result;
		} else {
			http_response_code(412);
		}
	} else {
		echo $verifyApp->message;
	}
});

// Метод восстановления пароля
Route::post('/v1/accounts/forgot_password', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();
		$result = AuthController::forgot_password($request->field('email'));

		if ($result) {
			http_response_code(200);

			echo $result;
		} else {
			http_response_code(404);
		}
	} else {
		echo $verifyApp->message;
	}
});

// Метод изменения пароля (сброса пароля)
Route::patch('/v1/users/update', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();
		$result = UserController::update_password($request->field('userid'), [
			'password' => $request->field('password'),
			'password_confirm' => $request->field('password_confirm')
		]);

		if ($result) {
			http_response_code(200);

			echo $result;
		} else {
			http_response_code(418);
		}
	} else {
		echo $verifyApp->message;
	}
});

// Метод получения данных пользователя
Route::get('/v1/users/get', function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$request = new Request();

		$token = $request->header('Authorization');
		$token = explode(' ', $token);

		if (AuthController::auth_verify($token[1])) {
			$result = UserController::get_data($token[1]);

			if ($result) {
				echo UserController::get_data($token[1]);
			} else {
				http_response_code(404);
			}
		} else {
			http_response_code(401);
		}
	} else {
		echo $verifyApp->message;
	}
});


Route::get('/', function() {
	var_dump(config('bond/jwt_secret'));
	
	echo 'Hello world';
});