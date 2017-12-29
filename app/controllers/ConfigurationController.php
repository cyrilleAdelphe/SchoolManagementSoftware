<?php

class ConfigurationController extends Controller
{
	public function removeGlobal($type)
	{
		if(Session::has($type))
			Session::forget($type);
	}

	public static function errorMsg($message, $overwrite = false)
	{
		if(Config::get('app.debug') || $overwrite)
		{
			return $message;
		}
		else
		{
			return 'Oops! Something went wrong';
		}
	}

	public static function translate($text)
	{
		$lang = strlen(trim(Input::get('language_used'))) ? Input::get('language_used') : 'en';
		return ConfigurationController::getTranslation($text, $lang);
	}

	public static function getTranslation($text, $lang)
	{
		return $text;
	}

	public static function recordTheNoOfAttempts($username, $role = 'admin', $delete = false)
	{

		if(!$delete)
		{
			//if module activated set to true
			$result = DB::table('failed_attempts')
				->where('username', '=', $username)
				->where('role', '=', $role)
				->where('is_active', '=', 'yes')
				->first();
			try
			{
				//DB::connection()->getPdo()->beginTransaction();
				
				if($result)
				{
					$no_of_minutes_passed = time() - $result->timestamp;
					$no_of_minutes_passed /= 60; //this gives no of minutes

					if((int)$result->no_of_attempts >= 3 && $no_of_minutes_passed <= 15)
					{
						$return = 'blocked';
					}
					else if((int)$result->no_of_attempts >= 3 && $no_of_minutes_passed > 15)
					{
						DB::connection()->getPdo()->beginTransaction();

						DB::table('failed_attempts')
							->where('id', $result->id)
							->update(array('is_active'=> 'no'));


						$dataToStore = array();
						$dataToStore['username'] = $username;
						$dataToStore['role'] = $role;
						$dataToStore['no_of_attempts'] = 1;
						$dataToStore['is_active'] = 'yes';
						$dataToStore['timestamp'] = time();

						DB::table('failed_attempts')
							->insert($dataToStore);

						DB::connection()->getPdo()->commit();

						$return = 'OK';
						
					}
					else
					{
						$result->no_of_attempts += 1;
						DB::table('failed_attempts')
							->where('id', $result->id)
							->update(array('no_of_attempts'=> $result->no_of_attempts));
						$return = 'OK';
					}
				}
				else
				{
					$dataToStore = array();
					$dataToStore['username'] = $username;
					$dataToStore['role'] = $role;
					$dataToStore['no_of_attempts'] = 1;
					$dataToStore['is_active'] = 'yes';
					$dataToStore['timestamp'] = time();

					DB::table('failed_attempts')
						->insert($dataToStore);
					$return = 'OK';
				}

				//DB::connection()->getPdo()->commit();
			}
			catch(PDOException $e)
			{
				return Redirect::route('error-500', array($e->getMessage(), $role));		
			}
		}
		else
		{
			$return = 'OK';
			try
			{
				DB::table('failed_attempts')
					->where('username', $username)
					->delete();	
			}
			catch(PDOException $e)
			{
				return Redirect::route('error-500', array($e->getMessage(), $role));		
			}
		}
		
		return $return;	
	}
}