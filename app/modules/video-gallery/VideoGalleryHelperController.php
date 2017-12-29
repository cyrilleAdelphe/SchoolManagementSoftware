<?php
define('VIDEO_GALLERY_CONFIG', app_path().'/modules/video-gallery/assets/config.json');

class VideoGalleryHelperController {
	// Curl helper function
	public static function curlGet($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$return = curl_exec($curl);
		curl_close($curl);
		return $return;
	}

	public static function getVideoData($video_id, $api_link) {
		$api_link = 'http://vimeo.com/api/v2/video/' . $video_id . '.xml';
	  $video_data = simplexml_load_string(VideoGalleryHelperController::curlGet($api_link));
	  return $video_data;
	}

	public static function getVimeoPlaylistItems($api_endpoint) {
		$config = VideoGalleryHelperController::getConfig();
		$vimeo_user_id = $config['vimeo_user_id'];
		return json_decode(VideoGalleryHelperController::curlGet($api_endpoint . $vimeo_user_id . '/videos.json'));
	}

	// get videos in the youtube playlist
	public static function getYoutubePlaylistItems($google_api_key) {
		$config = VideoGalleryHelperController::getConfig();
		$playlist_id = $config['youtube_playlist_id'];
		$page_token = ''; // the page token of the first page can be left blank
		$playlist_items = array();
		do {
			$playlist_data = json_decode(	
				VideoGalleryHelperController::curlGet('https://www.googleapis.com/youtube/v3/playlistItems?'.
					'part=snippet&'.
		      'maxResults=50&'.
		      'playlistId='. $playlist_id .'&'.
		      'pageToken=' . $page_token . '&' .
		      'key=' . $google_api_key
				)
			);

			$playlist_items = array_merge($playlist_items, $playlist_data->items);
			$page_token = isset($playlist_data->nextPageToken) ? $playlist_data->nextPageToken : '';

		} while ($page_token);

		return $playlist_items;
	}

	public static function getConfig() {
		if (File::exists(VIDEO_GALLERY_CONFIG)) {
			$config = json_decode(File::get(VIDEO_GALLERY_CONFIG), true);
		} else {
			$config = [
				'vimeo_user_id' => '',
				'youtube_playlist_id' => '',
			];
		}
		return $config;
	}

	public static function setConfig($data) {
		$config = array();
		if(File::exists(VIDEO_GALLERY_CONFIG))
		{
			$config = json_decode(File::get(VIDEO_GALLERY_CONFIG), true);
		}

		$config['vimeo_user_id'] = $data['vimeo_user_id'];
		$config['youtube_playlist_id'] = $data['youtube_playlist_id'];
		
		return File::put(VIDEO_GALLERY_CONFIG, json_encode($config, JSON_PRETTY_PRINT));

	}
}