<?php

class FacebookController extends Controller
{
public $response;
private $_token;

	public function getAccessToken()
	{

		session_start();
		echo '<pre>';
				$fb = new Facebook\Facebook([
		    'app_id' => FACEBOOK_APP_ID,   
		    'app_secret' => FACEBOOK_APP_SECRET,
		    'default_graph_version' => 'v2.2',
		]);
		$helper = $fb->getRedirectLoginHelper();

	    // We don't have the accessToken
	    // But are we in the process of getting it ? 
	    if (isset($_REQUEST['code'])) 
	    {

	        
	        try {
	            $accessToken = $helper->getAccessToken();
	            $facebook = json_decode(File::get(app_path().'/modules/facebook/facebook.json'));
				$facebook->_token = $accessToken;
				
				$facebook->images = $images;
			
				File::put(app_path().'/modules/facebook/facebook.json', json_encode($facebook));

	            } catch(Facebook\Exceptions\FacebookResponseException $e) {
	              // When Graph returns an error
	              echo 'Graph returned an error: ' . $e->getMessage();
	              exit;
	        } catch(Facebook\Exceptions\FacebookSDKException $e) {
	              // When validation fails or other local issues
	              echo 'Facebook SDK returned an error: ' . $e->getMessage();
	            exit;
	        }
		}
		else
        {
        	return View::make($this->view.'get-access-token')->with('fb', $fb);	
        }
	}

	public function getToken()
	{

	}

	public function getFacebookImages()
	{

		$fb = new Facebook\Facebook([
		    'app_id' => 'FACEBOOK_APP_ID',   
		    'app_secret' => 'FACEBOOK_APP_SECRET',
		    'default_graph_version' => 'v2.2',
		]);

		$facebook = json_decode(File::get(app_path().'/modules/gallery/facebook.json'));
		$token = $facebook->_token;

		try
		{

			dd($images);
			File::put(app_path().'/modules/gallery/facebook.json', json_encode($facebook));
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			//die();
			return Redirect::route('gallery-get-access-token');
		}
		

		return View::make($this->view.'facebook-gallery')
					->with('images', $images);
	}

	public function getAlbums($accessToken)
	{
		if (isset($accessToken)) 
	        {
	              // Logged in!
	              //Session::put('facebook_token', (string) $accessToken);
	              $facebook = File::get(app_path().'/modules/gallery/facebook.json');
	              $facebook = json_decode($facebook);
	              $facebook->_token = (string) $accessToken;


	              // Now you can redirect to another page and use the
	              // access token from $_SESSION['facebook_access_token']

	              $facebook->images = $fb->get('/1203225066407976/albums?fields=images.type(large)', $accessToken)->getGraphEdge()->asArray();

	       		File::put(app_path().'/modules/gallery/facebook.json', json_encode($facebook));

	       		return Redirect::route('gallery-get-facebook-images');
	       
	        }
	}

	public function getData($filename)
	{
		$this->response  = File::get(app_path().'/modules/gallery/'.$filename.'.json');
		$this->response = json_decode($this->response, true);
		return $this;
	}

	public function paginate($no_of_items, $start_page=1)
	{
		$data = $this->response;
		$this->response = array_splice($data['images'], ($start_page-1) * $no_of_items, ($start_page-1) * $no_of_items + $no_of_items);
		return $this;

	}	

	public function get()
	{
		return $this->response;
	}
}

