<?php

class ArticlesHelper
{
	private $module_name = 'articles';
	//private $config_file;

	private static $default_app_data = [
		'about_us'			=> '',
		'facebook_link'	=> '',
		'twitter_link'	=> '',
		'contact_number'=> '',
		'lat'						=> '',
		'lng'						=> '',
		'phone_number'	=> '',
		'address'				=> '',
		'email'					=> ''
	];
	 

	public function __construct()
	{
		//$this->config_file = app_path().'/modules/'.$this->module_name.'/config.json';
	}

	public function getConfig($config_file='')
	{
		$config_file = ($config_file=='')?app_path().'/modules/'.$this->module_name.'/config.json':$config_file;
		$config = json_decode(
						File::get($config_file), 
						true
					);

		return $config;

	}

	public function writeConfig($config,$config_file='')
	{
		$config_file = ($config_file=='')?app_path().'/modules/'.$this->module_name.'/config.json':$config_file;
		$result = File::put($config_file, json_encode($config));
		if ($result === false)
		{
		    $response['status'] = 'error';
		    $response['message'] = '';
		}
		else
		{
			$response['status'] = 'success';
		    $response['message'] = '';
		}

		return $response;
	}

	public static function categoryInfo()
	{
		/*
			| returns an associative array mapping from category_id to its corresponding title and the boolean frontend_publishable
		*/
		$categories = ArticleCategories::select('id','title','frontend_publishable')->get();
		$category_id_info = array();
		foreach($categories as $category)
		{
			$category_id_info[$category['id']] = array();

			$category_id_info[$category['id']]['title'] = $category['title'];
			$category_id_info[$category['id']]['frontend_publishable'] = $category['frontend_publishable'];
			// foreach($category as $key => $value)
			// {
			// 	$category_id_info[$category['id']][$key] = $value;
			// }
		}

		// echo '<pre>';
		// print_r($category_id_info);

		return $category_id_info;

	}

	public static function getImageLocation($data)
	{
		$img = '';
		preg_match_all('/(<img).*?(>)/i',$data->content, $img_tags); 

		if(!empty($img_tags) && !empty($img_tags[0]))
		{
			$image_dom = new DOMDocument;
			$image_dom->loadHTML($img_tags[0][0]);
			$images = $image_dom->getElementsByTagName('img');
			// there can be only one image!!
			foreach($images as $image)
			{
				$img = $image->getAttribute('src');
			}
			
		  // $img_tags[0][0] = str_replace(' ', '', $img_tags[0][0]);
		  // $img = substr($img_tags[0][0], strpos($img_tags[0][0], 'src="')+5, (strrpos($img_tags[0][0], '"')-15)); //15 is the magic number
		}

		return $img;
	}

	public static function getAppData()
	{
		$app_data_file = app_path() . '/modules/articles/app_data.txt';
		if ( File::exists($app_data_file) )
		{
			$app_data = json_decode(File::get($app_data_file), true);
			if (!$app_data)
			{
				$app_data = ArticlesHelper::$default_app_data;
			}
			else
			{
				foreach (ArticlesHelper::$default_app_data as $key => $value)
				{
					if (!isset($app_data[$key]))
					{
						$app_data[$key] = '';
					}
				}
			}
			return $app_data;
		}
		else
		{
			return ArticlesHelper::$default_app_data;
		}
	}

	public static function setAppData($app_data)
	{
		$new_app_data = array();
		foreach (ArticlesHelper::$default_app_data as $key => $value)
		{
			$new_app_data[$key] = (isset($app_data[$key])) ? $app_data[$key] : '';
		}

		$app_data_file = app_path() . '/modules/articles/app_data.txt';
		File::put($app_data_file, json_encode($new_app_data, JSON_PRETTY_PRINT));
	}

	public static function getArticleLink($article)
	{
		//if the article is a menu, show menu page
		$article_menu = Menus::where('article_id',$article['id'])->where('is_active','yes')->get();
	        				
	  if($article_menu->count())
	  	return URL::route('menu-view', array(Menus::where('article_id',$article['id'])->first()['id']));
	  else
	  	return URL::route('articles-view-get', array($article['id']));
	  
	}
}