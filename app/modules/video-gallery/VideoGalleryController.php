<?php

class VideoGalleryController extends BaseController
{
	protected $view = 'video-gallery.views.';

	protected $model_name = '';

	protected $module_name = 'video-gallery';

	public $current_user;

	public $role;

	private $vimeo_user_name = "Rakesh Shrestha";
	private $vimeo_user_id = 48861466;

	// The Simple API URL
	private $api_endpoint = 'http://vimeo.com/api/v2/';

	// attributes related to youtube api
	private $google_api_key = 'AIzaSyDDZVLVuVBBHhGRJcDDoFKWiFFse2nJdiM';
	private $youtube_playlist_id = 'PLMKA5kzkfqk18faruyGLVu1YI1-4i2N7P';
	
	
	public function test()
	{
		print '<pre>';
		print_r(json_decode(VideoGalleryHelperController::curlGet($this->api_endpoint. $this->vimeo_user_id . '/videos.json')));
		print '</pre>';
		
		// $video_data	 = VideoGalleryHelperController::getVideoData(155083647);
		// print '<pre>';
  // 	print_r($video_data);
  // 	print '</pre>';
  // 	print '<img src="'.$video_data->video->thumbnail_medium .'">';
	}

	public function show()
	{
		AccessController::allowedOrNot('video-gallery', 'can_view');
		$videos = VideoGalleryHelperController::getVimeoPlaylistItems($this->api_endpoint);

		return View::make($this->view . 'show-all')
						->with('videos', $videos);
	}

	public function showVideo($id)
	{
		AccessController::allowedOrNot('video-gallery', 'can_view');
		$videos = VideoGalleryHelperController::getVimeoPlaylistItems($this->api_endpoint);
		return View::make($this->view.'video')
						->with('video_id', $id)
						->with('videos', $videos);
	}

	public function youtubeGallery()
	{
		AccessController::allowedOrNot('video-gallery', 'can_view');
		
		return View::make($this->view . 'youtube-gallery')
							->with(
									'playlist_items', 
									VideoGalleryHelperController::getYoutubePlaylistItems($this->google_api_key)
							);
	}

	public function youtubeVideo($id)
	{
		AccessController::allowedOrNot('video-gallery', 'can_view');
		return View::make($this->view . 'youtube-video')
								->with('video_id', $id)
								->with(
										'playlist_items', 
										VideoGalleryHelperController::getYoutubePlaylistItems($this->google_api_key)
								);
	}

	public function getConfig()
	{
		AccessController::allowedOrNot('video-gallery', 'can_create,can_edit');
		return View::make($this->view . 'config')
			->with('config', VideoGalleryHelperController::getConfig());
	}

	public function postConfig()
	{
		AccessController::allowedOrNot('video-gallery', 'can_create,can_edit');

		$validator = Validator::make(
										Input::all(),
										array(
												'vimeo_user_id'	=> ['required'],
												'youtube_playlist_id'	=> ['required']
										)
									);
		if($validator->fails())
		{
			Session::flash('error-msg', 'Validation Error!!');
			return Redirect::back()
					->withInput()
					->withErrors($validator->messages());
		}
		
		if (VideoGalleryHelperController::setConfig(Input::all()))
		{
			Session::flash('success-msg', 'Configuration updated');
		}
		else
		{
			Session::flash('error-msg', 'Error updating Configuration');
		}

		return Redirect::back();
	}
}