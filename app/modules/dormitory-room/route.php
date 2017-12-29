<?php

Route::group(array('prefix'=>'dormitory', 'before'=>'reg-superadmin-admin'), function()
{
	Route::get('create-room', 
		[
			'as'	=> 'dormitory-room-create-get',
			'uses'	=> 'DormitoryRoomController@getCreateView'
		]);

	Route::get('edit-room/{id}', 
		[
			'as'	=> 'dormitory-room-edit-get',
			'uses'	=> 'DormitoryRoomController@getEditView'
		]);

	Route::get('list-rooms',
		[
			'as'	=>	'dormitory-room-list',
			'uses'	=>	'DormitoryRoomController@getListViewSimple'
		]);

	Route::group(array('before'=>'csrf'),function()
	{
		Route::post('create-room', 
			[
				'as'	=> 'dormitory-room-create-post',
				'uses'	=> 'DormitoryRoomController@postCreateView'
			]);

		Route::post('edit-room/{id}', 
			[
				'as'	=> 'dormitory-room-edit-post',
				'uses'	=> 'DormitoryRoomController@postEditView'
			]);

		Route::post('delete-room', 
			[
				'as'	=> 'dormitory-room-delete-post',
				'uses'	=> 'DormitoryRoomController@postDelete'
			]);

	});
});