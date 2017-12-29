<?php

class TestimonialQueue
{
	public function makeThumbnail($job, $data) //data has image
	{
		$img = Image::make($data['file']);
		$height = (int) (75);
		$width = (int) (75);

		$img->resize($height, $width);

		$img->save(app_path().'/modules/testimonial/asset/images/'.$data['id'].'.jpg');
		chmod(app_path().'/modules/testimonial/asset/images/'.$data['id'].'.jpg', 0777);
		//$job->delete();
	}
}