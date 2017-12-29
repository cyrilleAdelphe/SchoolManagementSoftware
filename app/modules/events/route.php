<?php
define('DIGITS_IN_EVENT_CODE', 5);

Route::get('event-calendar', [
	'as'	=>	'events-calendar-frontend',
	'uses'=>	'EventsController@getCalendarFrontend'
]);
Route::group(array('prefix'=>'/events','before'=>'reg-superadmin'),function(){
	
	Route::get('/create',
				['as'	=>	'events-create-get',
				 'uses'	=>	'EventsController@getCreateView']);

	Route::get('/list',
				['as'	=>	'events-list',
				 'uses'	=>	'EventsController@getListView']);

	Route::get('/view/{id}',
					['as'	=>	'events-view',
					 'uses'	=>	'EventsController@getViewview']);

	Route::get('/edit/{id}',
			['as'	=>	'events-edit-get',
			 'uses'	=>	'EventsController@getEditView']);

	Route::get('/remind/{id}', [
			'as'	=>	'events-send-notification',
			'uses'=>	'EventsController@remind'
		]);

	Route::get('/calendar',
				[
					'as'	=> 'events-calendar-get',
					'uses'	=> 'EventsController@getCalendar'
				]);

	Route::group(array('before' => 'csrf'), function(){

		Route::post('/create',
				['as'	=>	'events-create-post',
				 'uses'	=>	'EventsController@postCreateView']);

		Route::post('/edit/{id}',
				['as'	=>	'events-edit-post',
				 'uses'	=>	'EventsController@postEditView']);

		Route::post('/delete',
				['as'	=>	'events-delete-post',
				 'uses'	=>	'EventsController@deleteRows']);

		Route::post('/purge',
				['as'	=>	'events-purge-post',
				 'uses'	=>	'EventsController@purgeRows']);

		Route::post('/purge-record',
					['as'	=>	'events-purge-record-post',
					 'uses'	=>	'EventsController@postDelete']);
	});
});

?>