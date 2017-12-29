<?php


Route::get('/test',array(function(){
	echo Hash::make('password');
	//$words = str_word_count('hello world abc'."\n".'xyz abc',1);
	//print_r($words);
}));

Route::group(array('prefix' => 'articles'),function(){
	
	//view a single article
 	Route::get('/view/{id}',array(
 		'as' => 'articles-view-get',
 		'uses' => 'ArticlesController@getViewArticle'
 	));

 	Route::get('/{alias}',array(
 		'as' => 'articles-view-from-alias',
 		'uses' => 'ArticlesController@getViewArticleFromAlias'
 	));

 	Route::get('/view-category/{id}',array(
 		'as'	=> 'articles-view-category-get',
 		'uses'	=> 'ArticlesController@getViewCategory'
 	));

 	Route::group(array('before'=>'reg-content-manager', 'prefix' => 'backend'),function(){

 		Route::get('/edit/{id}/{alias}',array(
	 		'as' => 'articles-edit-get',
	 		'uses' => 'ArticlesController@getEdit'
	 	));

	 	Route::get('/main', array(
	 		'as' => 'articles-main-get',
	 		'uses' => 'ArticlesController@getMainView'
	 	));

	 	Route::get('/delete/{id}/{alias}',array(
	 		'as' => 'articles-delete-get',
	 		'uses' => 'ArticlesController@getDelete'
	 	));

	 	//show all the articles (not all, but the predefined number of articles)
	 	Route::get('/show-all',array(
	 		'as' => 'articles-show-all-get',
	 		'uses' => 'ArticlesController@getShowAll'
	 	)); 	

	 	

	 	Route::get('/config',array(
	 		//'before' => 'reg-superadmin',
	 		'as' => 'articles-config-get',
	 		'uses' => 'ArticlesController@getConfig'
	 	));

	 	Route::get('/config-category',array(
	 		//'before' => 'reg-superadmin',
	 		'as' => 'articles-category-config-get',
	 		'uses' => 'ArticlesController@getCategoryConfig'
	 	));

	 	Route::get('/add-category',array(
	 		'as' => 'articles-add-category-get',
	 		'uses' => 'ArticlesController@getAddArticleCategory'
	 	));

	 	Route::get('/edit-category/{id}/{title}',array(
	 		'as' => 'articles-edit-category-get',
	 		'uses' => 'ArticlesController@getEditCategory'
	 	));

	 	Route::get('/delete-category/{id}/{title}',array(
	 		'as' => 'articles-delete-category-get',
	 		'uses' => 'ArticlesController@getDeleteCategory'
	 	));

	 	Route::group(array('before' => 'csrf'), function() {

	 		Route::post('app-data-create', [
	 			'as'	=> 'articles-app-data-create-post',
	 			'uses'=> 'ArticlesController@postAppDataCreate'
	 		]);
	 		
	 		Route::post('/main',array(
	 			'as' => 'articles-create-post',
	 			'uses' => 'ArticlesController@postCreate'
	 		));

	 		Route::post('/edit',array(
		 		'as' => 'articles-edit-post',
		 		'uses' => 'ArticlesController@postEdit'
		 	));

		 	Route::post('/config',array(
		 		//'before' => 'reg-superadmin',
		 		'as' => 'articles-config-post',
		 		'uses' => 'ArticlesController@postConfig'
		 	));

		 	Route::post('/config-category',array(
		 		//'before' => 'reg-superadmin',
		 		'as' => 'articles-category-config-post',
		 		'uses' => 'ArticlesController@postCategoryConfig'
		 	));

		 	Route::post('/add-category',array(
		 		'as' => 'articles-add-category-post',
		 		'uses' => "ArticlesController@postAddArticleCategory"
		 	));
		 	
		 	Route::post('/edit-category',array(
		 		'as' => 'articles-edit-category-post',
		 		'uses' => 'ArticlesController@postEditCategory'
		 	));
	 	});
 	});

 });