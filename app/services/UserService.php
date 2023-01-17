<?php

namespace App\Services;

use App\Core\Handler;
use App\Database\Postgres;
use Http\Request;
use \Vudev\JsonWebToken\JWT;
use PHPMailer\PHPMailer\PHPMailer;

class UserService {

    /**
     * Метод регистрации пользователя
     * 
     * @access public
     * @return void
     */
    public function registration()
    {
        $request = new Request();
		$user = [
			'email' => $request->field('email'),
			'password' => $request->field('password'),
			'confirm_password' => $request->field('confirm_password'),
			'name' => $request->field('name'),
			'surname' => $request->field('surname')
		];

		$emailVerify = $this->verify_field($user['email'], 'email');

		if (is_array($emailVerify)) {
			return json_encode([
				'status' => 'warning',
				'code' => 0,
				'message' => $emailVerify[1]
			]);
		}

		if ($user['password'] !== $user['confirm_password']) {
			return json_encode([
				'status' => 'warning',
				'code' => 1,
				'message' => 'Passwords don\'t match',
				'fields' => $user
			]);
		}

		$pwd_hash = password_hash($user['password'], PASSWORD_DEFAULT);

		$sth = Postgres::prepare("
			SELECT 
				id 
			FROM 
				users 
			WHERE 
				email = ?
		", [
			$user['email']
		]);

		if (!$sth->rowCount()) {
			$core = new Handler;
			$USER_CONFIRM_CODE = $core->generate_code();

			Postgres::prepare("
				INSERT INTO 
					users 
				(
					email,
					password,
					activation,
					name,
					surname,
					token_secret_key,
					created_at,
					confirm_code,
					confirm_status,
					confirmed_at
				)
				VALUES (
					?,
					?,
					false,
					?,
					?,
					?,
					NOW()::timestamp,
					?,
					'waiting',
					NOW()::timestamp
				)
			", [
				$user['email'],
				$pwd_hash,
				$user['name'],
				$user['surname'],
				$core->generate_code(12, 'chars'),
				$USER_CONFIRM_CODE
			]);

			$lastInsertId = Postgres::lastInsertId();		

			if ($lastInsertId) {
				$sth = Postgres::prepare("SELECT id, to_char(confirmed_at, 'DD-MM-YYYY HH24:MI:SS') AS confirmed_at, email FROM users WHERE id = ?", [ $lastInsertId ]);
				$uData = Postgres::getAll($sth);

				$mail = new PHPMailer();

				$mail->setFrom('no-reply@butago.com', 'ButaGo');
				$mail->addAddress($uData['email']);
				
				$mail->CharSet = 'utf-8';
				$mail->Subject = 'Создание аккаунта - ButaGo';
				$mail->Body = '
					<h2>Создание аккаунта</h2>
					<h3>Спасибо, что присоединились к нам!</h3>
					<p>Подтвердите пожалуйста свой аккаунт на ButaGo</p>
					<h3><i>'.$USER_CONFIRM_CODE.'</i></h3>
					Ваш код подтверждения, актуален 5 минут от '.$uData['confirmed_at'].'
				';
				$mail->AltBody = "Создание аккаунта\r\nСпасибо, что присоединились к нам!\r\nПодтвердите пожалуйста свой аккаунт на ButaGo\r\n<b><i>".$USER_CONFIRM_CODE."</i></b>\r\n\r\nВаш код подтверждения, актуален 5 минут от ".$uData['confirmed_at'];

				$mail->send();

				http_response_code(201);

				return json_encode([
					'status' => 'success',
					'code' => 0,
					'message' => 'The user has been successfully created',
					'data' => [
						'userid' => $lastInsertId
					]
				]);

				return;
			} else {
				return json_encode([
					'status' => 'warning',
					'code' => 3,
					'message' => 'Sorry, the user was not created'
				]);
			}
		} else {
			return json_encode([
				'status' => 'warning',
				'code' => 2,
				'message' => 'A user with this email address already exists'
			]);
		}
    }

    /**
     * Метод авторизации пользователя
     * 
     * @access public
     * @return void
     */
    public function auth()
    {
        global $APPCFG_jwt_secret;

        $request = new Request();
        $user = [
            'email' => $request->field('email'),
            'password' => $request->field('password')
        ];
		
        // $pwd_hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $sth = Postgres::prepare("
            SELECT 
                id,
                password,
				email 
            FROM 
                users 
			WHERE 
                email = ?
        ", [
            $user['email']
        ]);

        if ($sth->rowCount()) {
            $uData = Postgres::getAll($sth);
            
            if (password_verify($user['password'], $uData['password'])) {
                $jwt = new JWT([
                    'payload' => [
                        'iss' => 'ButaGo Accounts',
                        'sub' => 'Auth',
                        'expiresIn' => '15min',
                        'user_id'=> $uData['id']
                    ],
                    'secret' => $APPCFG_jwt_secret
                ]);
                $access_token = $jwt->createToken();
                $refresh_token = $jwt->createToken([
                    'payload' => [
                        'expiresIn' => '30d'
                    ]
                ]);

                Postgres::prepare("
                    INSERT INTO tokens 
                    (
                        user_id,
                        access,
                        refresh,
						user_agent,
                        created_at
                    )
                    VALUES (
                        ?,
                        ?,
                        ?,
						?,
                        NOW()::timestamp
                    )
					ON CONFLICT (user_agent) DO 
					UPDATE SET created_at = NOW()::timestamp, access = '".$access_token."', refresh = '".$refresh_token."';
                ", [
                    $uData['id'],
                    $access_token,
                    $refresh_token,
					$request->user_agent()
                ]);

                $lastInsertId = Postgres::lastInsertId();

                if ($lastInsertId) {
					$parseRef = parse_url($request->referer());
					$host_name = $parseRef['host']; // .((isset($parseRef['port']) && !empty($parseRef['port'])) ? ':'.$parseRef['port'] : '');
					
					if (substr_count($host_name, '.') > 1) {
						$expl = explode('.', $host_name);

						unset($expl[array_key_first($expl)]);

						$host_name = implode('.', $expl);
					}

					$is_cookie = setcookie('butago.com:refresh_token', $refresh_token, [
						'expires' => time() + 60 * 60 * 24 * 30, 
						'path' => '/',
						'domain' => '.'.$host_name,
						'httponly' => true
					]);

					setcookie('butago.com:access_token', $access_token, [
						'expires' => time() + 60 * 60 * 24 * 30, 
						'path' => '/',
						'domain' => '.'.$host_name
					]);

					if ($is_cookie) {
						return json_encode([
							'status' => 'success',
							'code' => 1,
							'message' => 'Successful authorization',
							'data' => [
								'user_id' => $uData['id'],
								'access_token' => $access_token,
								'refresh_token' => $refresh_token,
								// 'host' => $host_name // temprary
							]
						]);
					} else {
						return json_encode([
							'status' => 'warning',
							'code' => 1,
							'message' => 'Don\'t save cookie httponly'
						]);
					}
                } else {
                    return json_encode([
                        'status' => 'success',
                        'code' => 0,
                        'message' => 'Successful authentication'
                    ]);
                }
            } else {
                return json_encode([
                    'status' => 'warning',
                    'code' => 0,
                    'message' => 'E-mail or password entered incorrectly'
                ]);
            }
        } else {
            return json_encode([
                'status' => 'warning',
                'code' => 0,
                'message' => 'E-mail or password entered incorrectly'
            ]);
        }
    }

	/**
     * Метод выхода пользователя
     * 
	 * @param string $token
     * @access public
     * @return boolean
     */
    public function logout($token)
    {
		// $sth = Postgres::prepare("
		// 	SELECT
		// 		user_id
		// 	FROM
		// 		tokens 
		// 	WHERE
		// 		refresh = ?
		// ", [
		// 	$token
		// ]);
		// $tokensData = Postgres::getAll($sth);
		
		$rm = $this->remove_token($token);

		if ($rm) {
			$request = new Request();
			$parseRef = parse_url($request->referer());
			$host_name = $parseRef['host']; // .((isset($parseRef['port']) && !empty($parseRef['port'])) ? ':'.$parseRef['port'] : '');
			
			if (substr_count($host_name, '.') > 1) {
				$expl = explode('.', $host_name);

				unset($expl[array_key_first($expl)]);

				$host_name = implode('.', $expl);
			}
			
			setcookie('butago.com:refresh_token', '', [
				'expires' => -1, 
				'path' => '/',
				'domain' => '.'.$host_name,
				'httponly' => true
			]);

			setcookie('butago.com:access_token', '', [
				'expires' => -1, 
				'path' => '/',
				'domain' => '.'.$host_name
			]);

			return true;
		} else {
			return false;
		}
    }

    /**
     * Метод валидности Access токена
     * 
     * @param string $token Access токен
     * @access public
     * @return void
     */
    public function auth_verify($token)
    {
		global $APPCFG_jwt_secret;

		$jwt = new JWT;
				
		return (!empty($token)) ? $jwt->verifyToken($token, $APPCFG_jwt_secret) : false;
    }

	/**
	 * Метод обновления пары токенов
	 * 
	 * @param string $token
	 * @param string $userAgent
	 * @access public
	 * @return string JSON объект
	 */
	public function refresh_token($token, $userAgent)
	{
		global $APPCFG_jwt_secret;
		
		$sth = Postgres::prepare("
			SELECT
				user_id,
				access
			FROM
				tokens 
			WHERE
				refresh = ?
		", [
			$token
		]);
		$tokensData = Postgres::getAll($sth);

		if (!empty($tokensData['access']) && !$this->auth_verify($tokensData['access'])) {
			$this->remove_token($token);

			$jwt = new JWT([
				'payload' => [
					'iss' => 'ButaGo Accounts',
					'sub' => 'Auth refresh',
					'expiresIn' => '15min',
					'user_id'=> $tokensData['user_id']
				],
				'secret' => $APPCFG_jwt_secret
			]);
			$access_token = $jwt->createToken();
			$refresh_token = $jwt->createToken([
				'payload' => [
					'expiresIn' => '30d'
				]
			]);

			Postgres::prepare("
				INSERT INTO tokens 
				(
					user_id,
					access,
					refresh,
					user_agent,
					created_at
				)
				VALUES (
					?,
					?,
					?,
					?,
					NOW()::timestamp
				)
				ON CONFLICT (user_agent) DO 
				UPDATE SET created_at = NOW()::timestamp, access = '".$access_token."', refresh = '".$refresh_token."';
			", [
				$tokensData['user_id'],
				$access_token,
				$refresh_token,
				$userAgent
			]);

			$lastInsertId = Postgres::lastInsertId();

			if ($lastInsertId) {
				$request = new Request();
				$parseRef = parse_url($request->referer());
				$host_name = $parseRef['host']; // .((isset($parseRef['port']) && !empty($parseRef['port'])) ? ':'.$parseRef['port'] : '');
				
				if (substr_count($host_name, '.') > 1) {
					$expl = explode('.', $host_name);

					unset($expl[array_key_first($expl)]);

					$host_name = implode('.', $expl);
				}

				$is_cookie = setcookie('butago.com:refresh_token', $refresh_token, [
					'expires' => time() + 60 * 60 * 24 * 30, 
					'path' => '/',
					'domain' => '.'.$host_name,
					'httponly' => true
				]);

				setcookie('butago.com:access_token', $access_token, [
					'expires' => time() + 60 * 60 * 24 * 30, 
					'path' => '/',
					'domain' => '.'.$host_name
				]);
				
				if ($is_cookie) {
					return json_encode([
						'status' => 'success',
						'code' => 0,
						'message' => 'Access and Refresh tokens have been successfully received',
						'data' => [
							'access_token' => $access_token,
							'refresh_token' => $refresh_token,
							// 'host' => $host_name // temprary
						],
						'token' => $token
					]);
				} else {
					return json_encode([
						'status' => 'warning',
						'code' => 1,
						'message' => 'Don\'t save cookie httponly'
					]);
				}
			} else {
				return json_encode([
					'status' => 'warning',
					'code' => 0,
					'message' => 'An error occurred while receiving Access and Refresh tokens'
				]);
			}
		}
	}

	/**
     * Метод восстановления доступа
     * 
	 * @param string $email
     * @access public
     * @return boolean|array
     */
    public function forgot_password($email)
    {
		$sth = Postgres::prepare("SELECT id FROM users WHERE email = ?", [ $email ]);

		if ($sth->rowCount()) {
			$core = new Handler;
			$uData = Postgres::getAll($sth);
			$USER_CONFIRM_CODE = $core->generate_code();

			$sth = Postgres::prepare("
				UPDATE users
				SET
					confirm_code = ?,
					confirm_status = 'waiting',
					confirmed_at = NOW()::timestamp
				WHERE
					id = ?
			", [
				$USER_CONFIRM_CODE,
				$uData['id']
			]);

			if ($sth) {
				$sth = Postgres::prepare("SELECT id, to_char(confirmed_at, 'DD-MM-YYYY HH24:MI:SS') AS confirmed_at FROM users WHERE id = ?", [ $uData['id'] ]);
				$uData = Postgres::getAll($sth);

				$mail = new PHPMailer();

				$mail->setFrom('no-reply@butago.com', 'ButaGo');
				$mail->addAddress($email);
				
				$mail->CharSet = 'utf-8';
				$mail->Subject = 'Восстановление доступа - ButaGo';
				$mail->Body = '
					<h2>Восстановление доступа</h2>
					<h3><i>'.$USER_CONFIRM_CODE.'</i></h3>
					Ваш код подтверждения, актуален 5 минут от '.$uData['confirmed_at'].'
				';
				$mail->AltBody = "Восстановление доступа\r\n\r\n<b><i>".$USER_CONFIRM_CODE."</i></b>\r\n\r\nВаш код подтверждения, актуален 5 минут от ".$uData['confirmed_at'];

				$mail->send();

				return json_encode([
					'status' => 'success',
					'code' => 0,
					'data' => [
						'userid' => $uData['id']
					]
				]);
			} else {
				return json_encode([
					'status' => 'warning',
					'code' => 0
				]);
			}
		} else {
			return false;
		}
    }

	/**
     * Метод подтверждения кода
     * 
	 * @param integer $userId
	 * @param string|integer $code
     * @access public
     * @return boolean|array
     */
    public function confirm_code($userId, $code, $activation)
    {
		$sth = Postgres::prepare("SELECT email, confirm_code, EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP::TIMESTAMP(0) - confirmed_at)) AS code_diff_time FROM users WHERE id = ?", [ $userId ]);

		if ($sth->rowCount()) {
			$uData = Postgres::getAll($sth);

			if ($uData['confirm_code'] === $code && (int)$uData['code_diff_time'] < 300) {
				Postgres::prepare("
					UPDATE users
					SET
						confirm_status = 'success',
						confirm_code = '',
						confirmed_at = NULL
						".(($activation) ? ', activation = true' : '')."
					WHERE
						id = ?
				", [ $userId ]);

				return json_encode([
					'status' => 'success',
					'code' => 0,
					'data' => [
						'userid' => $userId
					]
				]);
			} else {
				Postgres::prepare("
					UPDATE users
					SET
						confirm_status = 'error'
					WHERE
						id = ?
				", [
					$userId
				]);

				return json_encode([
					'status' => 'warning',
					'code' => 0,
					'message' => 'Invalid verification code'
				]);
			}
		} else {
			return false;
		}
    }

	/**
     * Метод изменения пароля (сброса)
     * 
	 * @param integer $userId
	 * @param array $data
     * @access public
     * @return boolean|array
     */
    public function update_password($userId, $data)
    {
		if ((int)$userId && sizeof($data)) {
			if ($data['password'] === $data['password_confirm']) {
				$pwd_hash = password_hash($data['password'], PASSWORD_DEFAULT);
				$sth = Postgres::prepare("
					UPDATE users
					SET
						password = ?,
						updated_at = NOW()::timestamp
					WHERE
						id = ?
				", [
					$pwd_hash,
					$userId
				]);

				if ($sth) {
					$sth = Postgres::prepare("SELECT id, email FROM users WHERE id = ?", [ $userId ]);
					$uData = Postgres::getAll($sth);

					$mail = new PHPMailer();

					$mail->setFrom('no-reply@butago.com', 'ButaGo');
					$mail->addAddress($uData['email']);
					
					$mail->CharSet = 'utf-8';
					$mail->Subject = 'Изменение пароля - ButaGo';
					$mail->Body = '
						<h2>Изменение пароля</h2>
						<h3>Ваш пароль был успешно изменен!</h3>
					';
					$mail->AltBody = "Восстановление доступа\r\n\r\nВаш пароль был успешно изменен!\r\n\r\n";

					$mail->send();

					return json_encode([
						'status' => 'success',
						'code' => 0
					]);
				} else {
					return json_encode([
						'status' => 'warning',
						'code' => 1,
						'message' => 'An error occurred while resetting your password'
					]);
				}
			} else {
				return json_encode([
					'status' => 'warning',
					'code' => 0,
					'message' => 'Passwords don\'t match'
				]);
			}
		} else {
			return false;
		}
    }

	/**
     * Метод получения данных пользователя
     * 
     * @param string $token Access токен
     * @access public
     * @return array
     */
    public function get_data($token)
    {
		$jwt = new JWT;
		$jwtdata = $jwt->json($token);

		$userId = (int)$jwtdata['payload']['user_id'];

		$sth = Postgres::prepare("
            SELECT 
                id,
				email,
				name,
				surname,
				name || ' ' || surname AS fullname,
				created_at
            FROM 
                users 
			WHERE 
                id = ?
        ", [
            $userId
        ]);

        if ($sth->rowCount()) {
			return json_encode([
				'status' => 'success',
				'code' => 0,
				'data' => Postgres::getAll($sth)
			]);
		} else {
			return false;
		}
    }

    /**
     * Метод валидации поля
     * 
     * @param string $data Проверяемое значение
     * @param string $type Тип проверки
     * @access public
     * @return void
     */
    protected function verify_field($data, $type)
	{
		$is_verify = true;
		$message = '';

		switch ($type) {
			case 'email':
				$pattern = '/^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,10}\.)?[a-z]{2,10}$/i';

				if (!preg_match($pattern, $data)) {
					$is_verify = false;
					$message = 'E-mail entered incorrectly';

					return [$is_verify, $message];
				}

				break;
		}

		return $is_verify;
	}

    /**
	 * Метод удаления токена
	 * 
	 * @param string $token
	 * @access private
	 * @return boolean Возвращает true если удалено, в противном случае вернет false
	 */
	private function remove_token($token)
	{
		$sth = Postgres::prepare("
			DELETE FROM
				tokens 
			WHERE
				refresh = ?
		", [
			$token
		]);

		// var_dump($sth->rowCount());  // 0 - false; >0 - true
		return ($sth->rowCount()) ? true : false;
	}
}