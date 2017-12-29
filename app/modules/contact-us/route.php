<?php
Route::group(array('prefix' => 'contact-us'),function(){
Route::group(array('group' => 'reg-superadmin-admin'), function()
{
		Route::get('/contact',
				['as'	=>	'contact-us-create-get',
				 'uses'	=>	'ContactUsController@getCreateView']);

		Route::group(array('before' => 'reg-content-manager'), function() {
			Route::get('/list',
					['as'	=>	'contact-us-list',
					 'uses'	=>	'ContactUsController@getListView']);
		
			Route::get('/view/{id}',
					['as'	=>	'contact-us-view',
					 'uses'	=>	'ContactUsController@getViewview']);

			Route::get('/edit/{id}',
					['as'	=>	'contact-us-edit-get',
					 'uses'	=>	'ContactUsController@getEditView']);

			Route::get('/config', [
					function(){
						return View::make('contact-us.views.changerecipient')
									->with('config',(new ContactUsHelper)->getConfig())
									->with('role', HelperController::getUserRole());
					},
					'as'=>'contact-us-config-get'
				]);
		});


		Route::group(array('before' => 'csrf'), function(){

			Route::post('/create',
					['as'	=>	'contact-us-create-post',
					 'uses'	=>	'ContactUsController@postCreateView']);

			Route::group(array('before' => 'reg-admin','before' => 'reg-superadmin'),function(){
				
				Route::post('/edit/{id}',
						['as'	=>	'contact-us-edit-post',
						 'uses'	=>	'ContactUsController@postEditView']);

				Route::post('/delete',
						['as'	=>	'contact-us-delete-post',
						 'uses'	=>	'ContactUsController@deleteRows']);

				Route::post('/purge',
						['as'	=>	'contact-us-purge-post',
						 'uses'	=>	'ContactUsController@purgeRows']);

				Route::post('/purge-record',
					['as'	=>	'contact-us-purge-record-post',
					 'uses'	=>	'ContactUsController@postDelete']);

				Route::post('/config',
					['as'	=>'contact-us-config-post',
					'uses'	=>'ContactUsController@postConfig'
					]);
			});

});	
		
		

		});
});