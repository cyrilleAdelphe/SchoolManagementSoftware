<?php
App::error(function(Illuminate\Session\TokenMismatchException $exception)
{
    Session::flash('error-msg', 'Your Session was expired');
    return Redirect::route('users-login');
});

App::error(function(Exception $e, $code){

	if(!in_array($code, array(404, 403)))
		return;

	$code = $code == 404 ? '404' : '500';
	$message = $code == '404' ? ConfigurationController::translate('Page Not Found') : ConfigurationController::translate('You are not allowed to view this page');
    //$role = HelperController::getUserRole();
    //switch ($role) 
    //{
      // case 'frontend':
       return View::make('frontend.'.$code)
       				->with('message', $message);

       /*default:
       //die('i am here');
       return View::make('backend.'.$role.'.'.$code)
       				->with('message', $message);
   }*/

});

