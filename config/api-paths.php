<?php
// Формируем постоянные ссылки для клиент-сервер запросов
return [
	// accounts
	'apiUrl_accounts_registration' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][2],
	'apiUrl_accounts_auth' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][0],
	'apiUrl_accounts_refresh_token' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][3],
	'apiUrl_accounts_auth_verify' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][1],
	'apiUrl_accounts_logout' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][4],
	'apiUrl_accounts_confirm_code' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][5],
	'apiUrl_accounts_forgot_password' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][6],
	'apiUrl_accounts_locale' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[0]).'/'.$APPCFG_methods[0][key($APPCFG_methods[0])][7],


	// users
	'apiUrl_users_get' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[1]).'/'.$APPCFG_methods[1][key($APPCFG_methods[1])][0],
	'apiUrl_users_update' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[1]).'/'.$APPCFG_methods[1][key($APPCFG_methods[1])][2],
	'apiUrl_users_update_pwd' => '/'.$APPCFG_versions[0].'/'.key($APPCFG_methods[1]).'/'.$APPCFG_methods[1][key($APPCFG_methods[1])][2]
];