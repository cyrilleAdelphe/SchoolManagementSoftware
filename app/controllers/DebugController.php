<?php

class DebugController extends Controller
{
	public function index()
	{
		
		return View::make('debug.debug');
	}

	public function check()
	{
		print_r(Input::all());
		die();
	}

	public static function errorMsg($message)
	{
		//check if debug mode on
		if(App::Config('get', true))
		{
			return $message;
		}
		else
		{
			return 'Oops! Something went wrong';
		}
	}
}

?>