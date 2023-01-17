<?php
use Core\Http\Request;
use Core\Facades\Http\Route;

use App\Controllers\VerifyAppController;

use App\Controllers\RegistrationController;
use App\Controllers\AuthController;
use App\Controllers\AuthRefreshTokenController;
use App\Controllers\UserController;

// Метод регистрации пользователя
Route::post($apiUrl_accounts_registration, function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$reg = new RegistrationController();

		echo $reg->result;
	} else {
		echo $verifyApp->message;
	}
});

// Метод авторизации пользователя
Route::post($apiUrl_accounts_auth, function() {
	$verifyApp = new VerifyAppController();

	if ($verifyApp->is_verify) {
		$auth = new AuthController();

		echo $auth->result;
	} else {
		echo $verifyApp->message;
	}
});

// Метод получения нового acceess и refresh токена
Route::get($apiUrl_accounts_refresh_token, function() {
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
Route::get($apiUrl_accounts_auth_verify, function() {
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
Route::get($apiUrl_accounts_locale, function() {
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
Route::get($apiUrl_accounts_logout, function() {
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
Route::post($apiUrl_accounts_confirm_code, function() {
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
Route::post($apiUrl_accounts_forgot_password, function() {
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
Route::post($apiUrl_users_update_pwd, function() {
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
Route::get($apiUrl_users_get, function() {
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


// Тестинг Куки
// Route::post($apiUrl_accounts_saveCookie, function() {
// 	setcookie('step-0:httponly', 'example-token', [
// 		'expires' => time() + 60 * 60 * 24 * 30, 
// 		'path' => '/',
// 		'domain' => '.butago.com',
// 		'httponly' => true
// 	]);
// });