<?php

define('GENERAL_SETTINGS', app_path() . '/modules/settings/config/general.json');
define('SCHOOL_LOGO_LOCATION', app_path().'/modules/settings/config');
define('SCHOOL_LOGO_FILENAME', 'school_logo');
define('SCHOOL_LOGO_URL', Config::get('app.url').'app/modules/settings/config/school_logo');

Route::group(array('prefix' => 'settings', 'before' => 'reg-superadmin'), function() 
{
	Route::get('general', 
		[
			'as'	=> 'settings-general-get',
			'uses'=> 'SettingsController@getGeneral'
		]
	);

	Route::group(array('before' => 'csrf'), function() {
		Route::post('general', 
			[
				'as'	=> 'settings-general-post',
				'uses'=> 'SettingsController@postGeneral'
			]
		);
	});
});