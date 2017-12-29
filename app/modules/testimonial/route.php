<?php

//Route::group(array('before' => 'reg-admin'), function()
//{
	Route::group(array('prefix' => 'testimonial', 'before' => 'reg-superadmin'), function(){

		Route::get('/list',
				['as'	=>	'testimonial-list',
				 'uses'	=>	'TestimonialController@getListView']);

		Route::get('/create',
				['as'	=>	'testimonial-create-get',
				 'uses'	=>	'TestimonialController@getCreateView']);

		Route::get('/view/{id}',
				['as'	=>	'testimonial-view',
				 'uses'	=>	'TestimonialController@getViewview']);

		Route::get('/edit/{id}',
				['as'	=>	'testimonial-edit-get',
				 'uses'	=>	'TestimonialController@getEditView']);

		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'testimonial-create-post',
					 'uses'	=>	'TestimonialController@postCreateView']);

			Route::post('/edit/{id}',
					['as'	=>	'testimonial-edit-post',
					 'uses'	=>	'TestimonialController@postEditView']);

			Route::post('/delete',
					['as'	=>	'testimonial-delete-post',
					 'uses'	=>	'TestimonialController@deleteRows']);

			Route::post('/purge',
					['as'	=>	'testimonial-purge-post',
					 'uses'	=>	'TestimonialController@purgeRows']);

			Route::post('/purge-record',
					['as'	=>	'testimonial-purge-record-post',
					 'uses'	=>	'TestimonialController@postDelete']);

		});
	
		Route::group(array('prefix' => 'ajax'), function()
		{
			Route::get('active-classes',
			[
				'as'	=>	'ajax-active-classes',
				'uses'	=>	'TeacherController@ajaxGetACtiveClasses'
			]);

			Route::get('active-sections',
			[
				'as'	=>	'ajax-active-sections',
				'uses'	=>	'TeacherController@ajaxGetACtiveSections'
			]);
		});
	});



//});

	