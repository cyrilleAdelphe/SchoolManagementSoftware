<?php

class ArticlesController extends BaseController
{
	protected $view = 'articles.views.';
	protected $model_name = 'Articles';
	protected $module_name = 'articles';

	public $current_user;

	function getEdit($id, $alias)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$article = Articles::find($id);
		$categories = ArticleCategories::where('id','!=',0)->get();
		if ($article['alias'] != $alias)
		{
			//TODO: display a proper popup message 
			echo 'Invalid edit operation.';
			die();
			
		}
		else
		{
			return View::make($this->view.'articleedit')
				->with('article', $article)
				->with('categories',$categories)
				->with('current_user', $this->current_user)
				->with('category_info',ArticlesHelper::categoryInfo());
		}
	}

	function postEdit()
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$data = Input::all();
		$result = $this->validateInput($data,true);

		$article = Articles::find(Input::get('id'));
		if (!$article)
		{
			//TODO: display a proper popup message 
			echo 'Invalid edit operation.';
			die();
			
		}

		if($article['alias'] != Input::get('alias'))//new alias
		{
			$result = $this->validateInput($data,false);
		}

		if($result['status'] == 'error')
		{
			return Redirect::route($this->module_name.'-edit-get',[$article['id'],$article['alias']])
						->withInput()
						->with('errors', $result['data'])
						->with('article',$article);
		}
		else
		{
			$database_result = Articles::cleanDatabaseOperation([ 
																	[$this,'updateInDatabase',[Input::all()]]
					
																]);
			if(!$database_result['success'])
			{
				echo 'Edit failed. Please try again later.';
				die();
			}
			else
			{
				return Redirect::route($this->module_name.'-main-get')
							->with('init_tab','list_article');
			}
		}
	}

	function getDelete($id,$alias)
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');

		$article = Articles::find($id);
		if ($article['alias'] != $alias)
		{
			//TODO: display a proper popup message 
			echo 'Invalid delete operation.';
			die();
		}
		else
		{
			$database_result = Articles::cleanDatabaseOperation([ 
																	[$article,'delete',null] 
																]);
			$success = $database_result['success'];
			$msg = $database_result['msg'];

		if (!$success)
			{
				//TODO: display a proper popup message 
				echo 'Failed. Try again later';
			}
			else
			{
				/*
					| The $init_tab doesn't get through the other side as a variable itself
					| But as a key in $_SESSION variable. Access it using Session::get('init_tab')
				*/

				return Redirect::route($this->module_name.'-main-get')
					->with('init_tab','list_article');
			}
		}
	}

	function getEditCategory($id,$title)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$category = ArticleCategories::find($id);
		if ($category['title'] != $title)
		{
			//TODO: display a proper popup message 
			echo 'Invalid edit operation.';
			die();
			
		}
		else
		{
			return View::make($this->view.'articleeditcategory')
				->with('current_user', $this->current_user)
				->with('category',$category);
		}
	}

	function postEditCategory()
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		$data = Input::all();
		$category = ArticleCategories::find(Input::get('id'));

		if (!$category)
		{
			//TODO: display a proper popup message 
			echo 'Invalid edit operation.';
			die();
			
		}

		if($category['title'] != Input::get('title'))//new title
		{
			$result = $this->validateInput($data,false,'ArticleCategories');//validate as if you are creating a new article (check for no duplicate alias)
		}
		else
		{
			$result = $this->validateInput($data,true,'ArticleCategories');//validate as if you're updating
		}

		if($result['status'] == 'error')
		{
			return Redirect::route($this->module_name.'-edit-category-get',[$category['id'],$category['title']])
						->withInput()
						->with('errors', $result['data'])
						->with('category',$category);
		}
		else
		{
			
			$category['title'] = Input::get('title');
			$category['frontend_publishable'] = Input::get('frontend_publishable');
			

			$database_result = Articles::cleanDatabaseOperation([ 
																	[$category,'save',null] 
					
																]);
			//display proper message to indicate success failure
			if(!$database_result['success'])
			{
				Session::flash('error-msg', 'Error. Try again later.');
				echo 'Edit failed. Please try again later.';
				die();
			}
			else
			{
				Session::flash('success-msg', 'Category Edited');
				
				return Redirect::route($this->module_name.'-main-get')
					->with('init_tab','list_category');
			}
		}	
	}

	function getDeleteCategory($id,$title)
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$category = ArticleCategories::find($id);
		if ($category['title'] != $title)
		{
			//TODO: display a proper popup message 
			echo 'Invalid delete operation.';
			die();
		}
		else
		{
			$child_articles = Articles::select('category_id')->where('category_id',$id)->first();
			if($child_articles)
			{
				//TODO: proper error prompt
				Session::flash('error-msg', 'Category contains article(s)! Delete the articles or change their category');
				return Redirect::route($this->module_name.'-main-get')
							->with('init_tab','list_category');
				
			}
			else
			{
				$database_result = Articles::cleanDatabaseOperation([ 
																		[$category,'delete',null] 
																	]);
				if($database_result['success'])
				{
					Session::flash('success-msg', 'Category Deleted');
					return Redirect::route($this->module_name.'-main-get')
							->with('init_tab','list_category');
				}
				else
				{
					
					echo 'Failure';
					return Redirect::route($this->module_name.'-main-get')
							->with('init_tab','list_category');
				}

			}
			

		}
	}

	function getMainView()
	{
		//TODO: find a good way to get all records
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$articles = Articles::where('id','!=',0)->get();
		$categories = ArticleCategories::where('id','!=',0)->get();

		return View::make($this->view.'articlemain')
			->with('articles', $articles)
			->with('categories',$categories)
			->with('current_user', $this->current_user)
			->with('category_info',ArticlesHelper::categoryInfo())
			->with('config',(new ArticlesHelper)->getConfig())
			->with('category_config',(new ArticlesHelper)->getConfig(app_path().'/modules/'.$this->module_name.'/configcategory.json'));
			
	}

	function postCreate()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$data = Input::all();
		

		$result = $this->validateInput($data);

		if($result['status'] == 'error')
		{
			//echo $result['data'];
			//die();
			return Redirect::route($this->module_name.'-main-get')
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		$database_result = Articles::cleanDatabaseOperation([
											[$this,'storeInDatabase',[$data]],
										]);
		$param = $database_result['param'];
		$success = $database_result['success'];
		$msg = $database_result['msg'];

		
		if ($success)
		{
			//echo 'Article added.';
			Session::flash('success-msg', 'Article added');
		}
		else
		{
			//echo 'Failure! Try Again.';
			Session::flash('error-msg', 'Error. Try again later.');
		}
		//die();
		return Redirect::route($this->module_name.'-main-get')
				->with('init_tab','list_article');
	}

	public function getShowAll()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		
		function getWords($text,$n)
		{
			//strip the html/php tags
			$text = strip_tags($text);
			
			$paragraph = explode (' ', $text);
			$paragraph = array_slice ($paragraph, 0, $n-1);
			return implode (' ', $paragraph);
		}

		//TODO: find a good way to get all the articles
		$articles = Articles::where('id','!=',0)->orderBy('created_at','DESC')->get();
		$config = (new ArticlesHelper)->getConfig();

		//find the category id of article that are publishable
		$categories = ArticleCategories::select('frontend_publishable','id')->where('frontend_publishable',1)->get();
		$publishable_categories = [];
		foreach($categories as $category)
		{
			$publishable_categories[] = $category['id'];
		}

		//The parts below would be in the blade template
		$i = 1;
		//echo '<pre>';
		foreach($articles as $article)
		{
			if(!in_array($article['category_id'],$publishable_categories))
			{
				continue;
			}
			echo $article['title']."<br/><br/>";
			preg_match_all('/(<img).*?(>)/i',$article['content'], $img_tags); 

			if(!empty($img_tags) && !empty($img_tags[0]))
			{
				echo($img_tags[0][0]);
			}
			else
			{
				echo '<img src="'.asset('public/assets/img/icons.png').'">"';
			}
			echo '<br/>';

			echo getWords($article['content'],$config['max_words'])."<br/>";
			echo '<a href ='.URL::route('articles-view-get',[$article['id']]).'> Read more... </a>';
			echo "<br/><br/>";

			
			if(++$i > $config['max_articles'])
			{
				break;
			}
		}

	}

	public function getViewArticle($id)
	{
		
		$article = Articles::where('id',$id)->first();

		return View::make('articles.views.articleview')
						->with('article',$article);

	}

	public function getViewCategory($id)
	{	

		AccessController::allowedOrNot($this->module_name, 'can_view');
		$config = json_decode(File::get(app_path() . '/modules/articles/configcategory.json'),true);
		$word_count =  $config['max_words'];
		$category = ArticleCategories::find($id);
		$articles = //Articles::where('category_id',$id)->take($config['max_articles'])->get();
			Articles::where('category_id',$id)->
			orderBy('created_at','DESC')->
			paginate($config['max_articles']);

		return View::make($this->view . 'articleviewcategory')
					->with('word_count',$word_count)
					->with('category',$category)
					->with('articles',$articles);
	}

	public function getAddArticleCategory()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		return View::make($this->view . 'articleaddcategory');
	}

	public function postAddArticleCategory()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');	
		$validator = Validator::make(Input::all(),(new ArticleCategories)->createRule);
		if($validator->fails())
		{
			return Redirect::route('articles-add-category-get')
					->withErrors($validator)
					->withInput();
		}

		$database_result = Articles::cleanDatabaseOperation([
											[$this,'storeInDatabase',[Input::all(),'ArticleCategories']]
										]);

		if ($database_result['success'])
		{
			echo 'Category added.';
			Session::flash('success-msg', 'Article added');
		}
		else
		{
			echo $database_result['msg'];
			Session::flash('error-msg', 'Error. Try again later.');
		}
		// die();
		return Redirect::route($this->module_name.'-main-get');
		

	}

	public function getConfig()
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$config = (new ArticlesHelper)->getConfig();
		return View::make($this->view.'articleconfig')
				->with('config',$config);
	}

	public function postConfig()
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$validator = Validator::make(Input::all(),
			array(
				'max_articles' => ['required','integer','min:1'],
				'max_words'	=>['required','integer','min:1']
			)
		);

		if($validator->fails())
		{
			//redirect with error message
			return Redirect::route('articles-config-get')
					->withErrors($validator)
					->withInput();
		}

		$helper = new ArticlesHelper;
		$config = $helper->getConfig();

		$config['max_articles'] = Input::get('max_articles');
		$config['max_words'] = Input::get('max_words');

		$result = $helper->writeConfig($config);

		if ($result['status'] == 'error')
		{
			Session::flash('error-msg','Error updating configuration');
		}
		else
		{
			Session::flash('success-msg','Configuration updated');
		}
		return Redirect::route('articles-main-get')
				->with('init_tab','featured_article_manager');
		
	}

	public function getCategoryConfig()
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$config = (new ArticlesHelper)->getConfig(app_path().'/modules/'.$this->module_name.'/configcategory.json');
		return View::make($this->view.'articlecategoryconfig')
				->with('category_config',$config);
	}

	public function postCategoryConfig()
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$validator = Validator::make(Input::all(),
			array(
				'max_articles' => ['required','integer','min:1'],
				'max_words'	=>['required','integer','min:1']
			)
		);

		if($validator->fails())
		{
			//redirect with error message
			return Redirect::route('articles-config-get')
					->withErrors($validator)
					->withInput();
		}

		$helper = new ArticlesHelper;
		$config = $helper->getConfig(app_path().'/modules/'.$this->module_name.'/configcategory.json');

		$config['max_articles'] = Input::get('max_articles');
		$config['max_words'] = Input::get('max_words');

		$result = $helper->writeConfig($config,app_path().'/modules/'.$this->module_name.'/configcategory.json');

		if ($result['status'] == 'error')
		{
			Session::flash('error-msg','Error updating configuration');
		}
		else
		{
			Session::flash('success-msg','Configuration updated');
		}
		return Redirect::route('articles-main-get')
				->with('init_tab','featured_category_manager');
		
	}

	public function postAppDataCreate()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$data = Input::all();
		ArticlesHelper::setAppData($data);
		Session::flash('success-msg', 'App data updated');
		return Redirect::route('articles-main-get')
						->with('init_tab', 'app_data');
	}

	//these are frontend urls
	public function getViewArticleFromAlias($alias)
	{
		$article = Articles::where('alias',$alias)->first();
		$menu = Menus::where('article_id', $article->id)->first();
		
		
		return View::make('menu.views.menuview')
						->with('article',$article)
						->with('menu', $menu);	
		
		
	}

}