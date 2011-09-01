<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Recaptcha 
{
		protected static $_instance;
		protected static $_config;
		
		private function __construct($config)
		{
			require_once(MODPATH.'recaptcha/lib/recaptchalib.php');
			
			self::$_config = $config;
		}
		public static function instance($instance='default')
		{
			if ( ! isset(Recaptcha::$_instance))
			{				
				$config = Kohana::config('recaptcha.'.$instance);
				
				require_once(MODPATH.'recaptcha/lib/recaptchalib.php');
				
				Recaptcha::$_instance = new Kohana_recaptcha($config); 
			}

			return self::$_instance;
		}
		
		public function get_html($publickey = NULL)
		{
			if ( ! $publickey)
			{
				$publickey = self::$_config['publickey'];
				
				if ($publickey === NULL)
				{
					throw new Kohana_Exception('You must specify a reCAPTCHA public key');
				}
			}
			
			return recaptcha_get_html($publickey);
		}
		
		public function is_valid($challenge_field = '', $response_field = '', $privatekey = NULL, $type = 'bool')
		{
			if ( ! $privatekey) 
			{ 
				$privatekey = self::$_config['privatekey'];
				
				if ($privatekey === NULL)
				{
					throw new Kohana_Exception('You must specify a reCAPTCHA private key');
				}
			}
			
			$url = $_SERVER['REMOTE_ADDR'];
			
			$result = recaptcha_check_answer($privatekey, $url, $challenge_field, $response_field);
									
			return ($type === 'object') ? $result : $result->is_valid; 
		}
}