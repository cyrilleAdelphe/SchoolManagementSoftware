<?php

	Route::group(array('prefix' => 'daily-routine'), function()
	{

		Route::get('/list', 
			['as'	=>	'daily-routine-list',
			 'uses'	=>	'DailyRoutineController@dailyRoutineList']);

		Route::get('/create', 
			['as'	=>	'daily-routine-create',
			 'uses'	=>	'DailyRoutineController@getCreateDailyRoutine']);

		Route::get('/edit', 
			['as'	=>	'daily-routine-edit',
			 'uses'	=>	'DailyRoutineController@getEditDailyRoutine']);

		Route::group(array('before' => 'csrf'), function()
		{
			Route::post('/create', 
			['as'	=>	'daily-routine-create-post',
			 'uses'	=>	'DailyRoutineController@postCreateDailyRoutine']);

			Route::post('/edit', 
				['as'	=>	'daily-routine-edit-post',
				 'uses'	=>	'DailyRoutineController@postEditDailyRoutine']);

			Route::post('/delete', 
				['as'	=>	'daily-routine-purge-record-post',
				 'uses'	=>	'DailyRoutineController@postDelete']);

			Route::post('/delete-day', [
				'as'	=>	'daily-routine-delete-day-post',
				'uses'=>	'DailyRoutineController@postDeleteDay'
			]);

		});

	});