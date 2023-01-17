<?php

namespace App\Services;

use App\Database\Postgres;

class VerifyAppService {
    /**
     * @var boolean
     */
    public $is_verify = false;

    /**
     * @var string
     */
	public $message = '';

    /**
     * Конструктор
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        global $APPCFG_connections;

		$userId = $APPCFG_connections['user_id'];
		$sKey = $APPCFG_connections['secret_key'];
		$domain = $APPCFG_connections['domain'];

		$sth = Postgres::prepare("
			SELECT 
				id 
			FROM 
				apps 
			WHERE 
				user_id = ? AND 
				secret_key = ? AND 
				domain = ? 
		", [
			$userId, 
			$sKey, 
			$domain
		]);

		if ($sth->rowCount()) {
			$this->is_verify = true;
		} else {
			$this->is_verify = false;
			$this->message = json_encode([
				'status' => 'error',
				'code' => 403,
				'message' => 'Access is denied!'
			]);
		}
    }
}