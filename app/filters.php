<?php
/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{

});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!Auth::user()->check() && !Auth::admin()->check() && !Auth::superadmin()->check())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/**************** for ajax filters ****************************/
Route::filter('csrf-ajax', function()
{
    if (Session::token() != Request::header('x-csrf-token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

/**************************************************************************************/
Route::filter('reg-user', function()
{
	if(Auth::user()->guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			Session::put('error-msg', 'You must log in first');
			return Redirect::route('users-login');
		}
	}
});

Route::filter('guest-user', function()
{
	if(Auth::user()->check()) 
		{
			Session::put('info-msg', 'You are already logged in');
			return Redirect::route('users-home');
		}
});
/****************************************************************************************/

/**************************************************************************************/
Route::filter('reg-admin', function()
{
	if(Auth::admin()->guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			Session::put('error-msg', 'You must log in first');
			return Redirect::route('users-login');
		}
	}
});

Route::filter('guest-admin', function()
{
	if(Auth::admin()->check()) 
		{
			Session::put('info-msg', 'You are already logged in');
			return Redirect::route('admin-home');
		}
});
/****************************************************************************************/

/**************************************************************************************/
Route::filter('reg-superadmin', function()
{
	if(Auth::superadmin()->guest() && Auth::admin()->guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			Session::put('error-msg', 'You must log in first');
			return Redirect::route('superadmin-login');
		}
	}
});

Route::filter('reg-superadmin-admin', function()
{

	if(Auth::superadmin()->guest() && Auth::admin()->guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			Session::put('error-msg', 'You must log in first');
			return Redirect::route('users-login');
		}
	}

});

Route::filter('reg-superadmin-admin-user', function()
{
	HelperController::loginWithToken();
	if(Auth::superadmin()->guest() && Auth::admin()->guest() && Auth::user()->guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			Session::put('error-msg', 'You must log in first');
			return Redirect::route('users-login');
		}
	}

});

Route::filter('guest-superadmin', function()
{
	if(Auth::superadmin()->check()) 
		{
			Session::put('info-msg', 'You are already logged in');
			return Redirect::route('superadmin-home');
		}
});
/****************************************************************************************/
/****************************************************************************************/