<?php
Route::group(array('prefix' => 'transportation'), function()
{
	Route::get('/search-student', [
		'as' => 'search-transportation-student',
		'uses' => 'TransportationController@getAssignedStudentList'			
		]);
		
	Route::post('add-locations', 
	[ 'as'	=>	'tranporation-add-locations',
	  'uses'=>	'TransportationController@addLocations']);

	Route::get('view-locations/{unique_transportation_id}',
		['as' => 'transportation-view-locations',
		 'uses' => 'TransportationController@viewLocations']);

	Route::get('make-xml/{unique_transportation_id}',
		['as'	=>	'transportation-make-xml',
		 'uses'	=>	'TransportationController@makeXml']);

	Route::get('cron/delete-locations/{unique_transportation_id?}',
		['as'	=>	'transportation-delete-locations',
		'uses'	=>	'TransportationController@deleteLocations']);

	Route::get('create',
		['as'	=>	'transportation-create-get',
		 'uses'	=>	'TransportationController@getCreateView']);

	Route::get('edit/{id}',
			['as'	=>	'transportation-edit-get',
			 'uses'	=>	'TransportationController@getEditView']);

	Route::get('/list',
		['as'	=>	'transportation-list',
		 'uses'	=>	'TransportationController@getListView']);

	Route::get('edit-assign-students/{id}',
			['as'	=>	'transportation-edit-assign-students-get',
			 'uses'	=>	'TransportationController@getEditAssignStudents']);

	Route::get('edit-assign-staffs/{id}',
		[	'as'	=> 'transportation-edit-assign-staffs-get',
			'uses'	=> 'TransportationController@getEditAssignStaffs'

		]);
	Route::get('transportation-staff', [
			'as'	=> 'transportation-staff-list',
			'uses'	=> 'TransportationController@getTransportationStaff'
		]);

	Route::get('transportation-staff-location/{unique_transportation_id}',
		[	'as'	=> 'transportation-view-staff',
			'uses'  => 'TransportationController@getViewStaffVechileLocation'

		]);


	Route::group(array('before' => 'csrf'), function()
	{
		Route::get('student-list',
			['as' => 'ajax-transportation-student-list',
			 'uses' => 'TransportationController@getStudentList']);

		Route::post('create',
		['as'	=>	'transportation-create-post',
		 'uses'	=>	'TransportationController@postCreateView']);

		Route::post('edit/{id}',
			['as'	=>	'transportation-edit-post',
			 'uses'	=>	'TransportationController@postEditView']);

		Route::post('/delete-trasnportation/{id}',
					['as'	=>	'transportation-delete-single-post',
					 'uses'	=>	'TransportationController@deleteTransportation']);

		Route::post('/delete-trasnportation-student/{id}',
					['as'	=>	'transportation-student-delete-single-post',
					 'uses'	=>	'TransportationController@deleteTransportationStudent']);

		Route::post('/delete',
					['as'	=>	'transportation-delete-post',
					 'uses'	=>	'TransportationController@deleteRows']);

		Route::post('/purge',
				['as'	=>	'transportation-purge-post',
				 'uses'	=>	'TransportationController@purgeRows']);

		Route::post('assign-students',
			['as'	=>	'transportation-assign-students',
			 'uses'	=>	'TransportationController@postAssignStudent']);

		Route::post('edit-assign-students/{id}',
			['as'	=>	'transportation-edit-assign-students-post',
			 'uses'	=>	'TransportationController@postEditAssignStudents']);

		Route::post('assign-staffs', [
			'as'	=> 'transportation-assign-staffs',
			'uses'	=> 'TransportationController@postAssignStaffs'
			]);

		Route::post('edit-assign-staffs/{id}',[
			'as'	=> 'transportation-edit-assign-staffs-post',
			'uses'	=> 'TransportationController@postEditAssignStaffs'
					]);

		Route::post('delete-assign-staff/{id}', [
			'as'	=> 'transportation-staff-delete-single-post',
			'uses'	=> 'TransportationController@postDeleteAssginedStaff'
			]);

	});
});
	
?>