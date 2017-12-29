<?php

class SlidesQueue
{
	public function makeThumbnail($job, $data) //data has image
	{
		$img = Image::make($data['file']);
		$img->save(app_path().'/modules/slides/asset/images/'.$data['id'].'.jpg');
		//chmod(app_path().'/modules/slides/asset/images/'.$data['id'].'.jpg', 0644);
		//$job->delete();
	}
}