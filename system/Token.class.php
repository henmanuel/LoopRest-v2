<?php

require_once DIRECTORY . 'vendor/autoload.php';

use Firebase\JWT\JWT;

class Token
{
	protected $auth;
	private static $aud = null;

	/**
	 * Generate token using JWT for using request any data in the system
	 *
	 * @param $data
	 * @return string
	 */
	public static function signIn($data)
	{
		$time = time();
		$token = array('exp' => $time + (60 * 60), 'aud' => self::aud(), 'data' => $data);

		return JWT::encode($token, CoreConfig::SECRET_KEY);
	}

	/**
	 * Generate unique key identity host
	 *
	 * @return string
	 */
	private static function aud()
	{
		$model = Model::getInstance();
		$server = $model->getClientServerInstance;
		$aud = $server->getIp();
		$aud .= @$_SERVER['HTTP_USER_AGENT'];
		$aud .= gethostname();

		return sha1($aud);
	}

	/**
	 * Check if token is valid from a user to de system
	 *
	 * @param $token
	 * @return string
	 */
	public static function check($token)
	{
		try {
			$decode = JWT::decode($token, CoreConfig::SECRET_KEY, CoreConfig::ENCRYPT);

			if($decode->aud !== self::aud()){
				throw new Exception('Invalid user logged in.');
			}

			if(empty($token)){
				throw new Exception('Invalid token supplied.');
			}

			return true;

		}catch(\Firebase\JWT\ExpiredException $e){
			return 'expired token';
		}catch(\Firebase\JWT\SignatureInvalidException $e){
			return 'Corrupted sign';
		}catch(\Exception $e){
			return 'Security error token';
		}
	}

	/**
	 * Decrypt user information in token
	 *
	 * @param $token
	 * @return mixed
	 */
	public static function getData ($token)
	{
		$decode = JWT::decode($token, CoreConfig::SECRET_KEY, CoreConfig::ENCRYPT);

		return $decode->data;
	}
}