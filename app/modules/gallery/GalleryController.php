<?php
define("FACEBOK_GALLERY_LOCATION", app_path().'/modules/gallery/facebook.json');
class GalleryController extends BaseController
{
	protected $view = 'gallery.views.';

	protected $model_name = 'Gallery';

	protected $module_name = 'gallery';

	public $current_user;

	public $role;

	public $columnsToShow = array(
									array(
										'column_name' 	=> 'title',
										'alias'			=> 'Title'
									),
									array(
										'column_name' 	=> 'description',
										'alias'			=> 'Description'
									),
									array(
										'column_name' 	=> 'category_name',
										'alias'			=> 'Category'
									),
									array(
										'column_name' 	=> 'id',
										'alias'			=> 'Image'
									),
								 );

	public function showCategory($category_id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$category = GalleryCategory::find($category_id);
		$model = $this->model_name;
		if ($category)
		{
			$images = $model::where('category_id', $category_id)
				->select('id')
				->get();

			return View::make($this->view . 'show-category')
				->with('images', $images)
				->with('category', $category);
		}
		else
		{
			App::abort(404);
		}
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		foreach($data['images'] as $image)
		{
			$data['image'] = $image;

			$result = $this->validateInput($data);
			
			if($result['data'] && $result['data']->has('image'))
			{
				$result['data']->add('images', $result['data']->first('image'));
			}

			if($result['status'] == 'error')
			{
				Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
				
				return Redirect::route($this->module_name.'-create-get')
							->withInput()
							->with('errors', $result['data']);
			}
		}

		try
		{
			foreach($data['images'] as $image)
			{
				$data['image'] = $image;

				$id = $this->storeInDatabase($data);	

				$img_original = Image::make($image);
				$img_original->save(GALLERY_ORIGINAL_FOLDER.'/'.$id);
				$img_original->crop((int)($img_original->width()/2), (int)($img_original->height()/2))->save(GALLERY_THUMBNAILS_FOLDER . '/'.$id);
				
			}

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
		}
		catch(Exception $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data, true);

		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			
			return Redirect::route($this->module_name.'-edit-get', array($id))
						->withInput()
						->with('errors', $result['data']);
		}
		
		try
		{
			$id = $this->updateInDatabase($data);	

			if(Input::hasFile('image'))
			{
				$img_original = Image::make(Input::file('image'));
				$img_original->save(GALLERY_ORIGINAL_FOLDER.'/'.$id);
				$img_original->crop((int)($img_original->width()/2), (int)($img_original->height()/2))->save(GALLERY_THUMBNAILS_FOLDER . '/'.$id);
			}

			$success = true;
			$msg = 'Record successfully updated';
			$param['id'] = $id; 
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
			$param['id'] = $data['id'];
		}
		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}

	public function getListView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$model = new $this->model_name;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns();
		$tableHeaders = $this->getTableHeader();
		//$actionButtons = $this->getActionButtons();
		$queries = $this->getQueries();

		return View::make($this->view.'list')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders)
					->with('paginateBar', $this->getPaginateBar())
					//->with('actionButtons', $actionButtons)
					->with('role', $this->role);

	}

	public function postDelete()
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$model = new $this->model_name;
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			
			if(File::exists(GALLERY_ORIGINAL_FOLDER.'/'.$id))
			{
				File::delete(GALLERY_ORIGINAL_FOLDER.'/'.$id);
			}
			
			if(File::exists(GALLERY_THUMBNAILS_FOLDER.'/'.$id))
			{
				File::delete(GALLERY_THUMBNAILS_FOLDER.'/'.$id);
			}

			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

	public function getAccessToken()
	{
		session_start();
			$fb = new Facebook\Facebook([
		    'app_id' => FACEBOOK_APP_ID,   
		    'app_secret' => FACEBOOK_APP_SECRET,
		    'default_graph_version' => 'v2.8',
		]);
		$helper = $fb->getRedirectLoginHelper();

	    // We don't have the accessToken
	    // But are we in the process of getting it ? 
	    if (isset($_REQUEST['code'])) 
	    {

	        
	        try {
	            $accessToken = $helper->getAccessToken();
	            $facebook = json_decode(File::get(FACEBOK_GALLERY_LOCATION), true);
				$facebook['_token'] = (string) $accessToken;
				$albums = $fb->get('/'.FACEBOOK_CLIENT_ID.'/albums', $accessToken)->getGraphEdge();
				
				//$facebook->albums = $albums;

				$facebook_albums = array();
				foreach($albums as $a)
				{
					//$facebook_albums[] = $a['id'];
					$facebook_albums[$a['id']]['name'] = $a['name'];
					$images = $fb->get('/'.$a['id'].'/photos?fields=images.type(large)', $accessToken)->getGraphEdge()->asArray();
					
					foreach($images as $img)
					{
						$facebook_albums[$a['id']]['images'][$img['id']]['image_small'] = end($img['images'])['source']	;
						$facebook_albums[$a['id']]['images'][$img['id']]['image_large'] = $img['images'][0]['source']	;
						$facebook_albums[$a['id']]['images'][$img['id']]['show'] = 'yes';
					}
				}

				$facebook['albums'] = $facebook_albums;
				File::put(FACEBOK_GALLERY_LOCATION, json_encode($facebook, JSON_PRETTY_PRINT));
				
	            } catch(Facebook\Exceptions\FacebookResponseException $e) {
	              // When Graph returns an error
	              echo 'Graph returned an error: ' . $e->getMessage();
	              exit;
	        } catch(Facebook\Exceptions\FacebookSDKException $e) {
	              // When validation fails or other local issues
	              echo 'Facebook SDK returned an error: ' . $e->getMessage();
	            exit;
	        }

	        
	       		return Redirect::route('gallery-get-facebook-albums');
		}
		else
        {
        	return View::make($this->view.'get-access-token')->with('fb', $fb);	
        }
	}

	public function getFacebookAlbums()
	{
		$facebook = json_decode(File::get(FACEBOK_GALLERY_LOCATION));
		if(count($facebook) == 0)
		{
			return Redirect::route('gallery-get-access-token');
		}

		$accessToken = $facebook->_token;
		$fb = new Facebook\Facebook([
		    'app_id' => FACEBOOK_APP_ID,   
		    'app_secret' => FACEBOOK_APP_SECRET,
		    'default_graph_version' => 'v2.8',
		]);
		
		try
		{
			
			$albums = $fb->get('/'.FACEBOOK_CLIENT_ID.'/albums', $accessToken)->getGraphEdge();
			
			return View::make($this->view.'facebook-albums')
					->with('albums', $albums);

		}
		catch(Facebook\Exceptions\FacebookResponseException $e)
		{
			return Redirect::route('gallery-get-access-token');
		}
		catch(Exception $e)
		{
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back();
		}
		
	}

	public function getFacebookImages()
	{
		$album_id = Input::get('facebook_album_id', 0);
		$album_name = Input::get('facebook_album_name', 'Untitled');

		$fb = new Facebook\Facebook([
		    'app_id' => FACEBOOK_APP_ID, 
		    'app_secret' => FACEBOOK_APP_SECRET,
		    'default_graph_version' => 'v2.8',
		]);

		$facebook = json_decode(File::get(FACEBOK_GALLERY_LOCATION), true);
		$token = $facebook['_token'];

		try
		{

			$images = $fb->get('/'.$album_id.'/photos?fields=images.type(large)', $token)->getGraphEdge()->asArray();
			//dd($images);
			return View::make($this->view.'facebook-gallery')
					->with('images', $images)
					->with('album_name', $album_name)
					->with('album_id', $album_id)
					->with('facebook', $facebook);
			
		}
		catch(Facebook\Exceptions\FacebookResponseException $e)
		{
			echo $e->getMessage();
			die();
			return Redirect::route('gallery-get-access-token');
		}
		catch(Exception $e)
		{

			Session::flash('error-msg', $e->getMessage());
			return Redirect::back();
		}
	}

	public function postFacebookImages()
	{
		$facebook = json_decode(File::get(FACEBOK_GALLERY_LOCATION), true);
		$input = Input::all();
		
		$facebook['albums'][$input['facebook_album_id']]['name'] = $input['facebook_album_name'];

		foreach($input['image_small'] as $index => $i)
		{
				$facebook['albums'][$input['facebook_album_id']]['images'][$index]['image_small'] = $i;
				$facebook['albums'][$input['facebook_album_id']]['images'][$index]['image_large'] = $input['image_large'][$index];
				$facebook['albums'][$input['facebook_album_id']]['images'][$index]['show'] = $input['show'][$index];
			
		}

		File::put(FACEBOK_GALLERY_LOCATION, json_encode($facebook, JSON_PRETTY_PRINT));
		
		$url = route('gallery-get-facebook-images')."?facebook_album_id=".$input['facebook_album_id'].'&facebook_album_name='.$input['facebook_album_name'];
		return Redirect::to($url);
		//foreach($input as $i)
	}

	public function frontendShowAlbums()
	{
		$facebook = json_decode(File::get(FACEBOK_GALLERY_LOCATION), true);
		$albums = [];
		foreach($facebook['albums'] as $album_id => $data)
		{
			$temp = [];
			$temp['name'] = $data['name'];
			$temp['id'] = $album_id;
			$temp['image'] = isset($data['images']) ? array_values($data['images'])[0]['image_small'] : '';
			$albums[] = $temp;
		}

		return View::make($this->view.'frontend-facebook.show-albums')
					->with('albums', $albums);
	}

	public function frontendShowPhotos($album_id)
	{
		$facebook = json_decode(File::get(FACEBOK_GALLERY_LOCATION), true);
		$images = isset($facebook['albums'][$album_id]) ? $facebook['albums'][$album_id]['images'] : [];
		$album_name = isset($facebook['albums'][$album_id]['name']) ? $facebook['albums'][$album_id]['name'] : '';

		foreach($images as $index => $img)
		{
			if($img['show'] == 'no')
				unset($images[$index]);
		}


		return View::make($this->view.'frontend-facebook.show-images')
					->with('images', $images)
					->with('album_id', $album_id)
					->with('album_name', $album_name);
	}
}