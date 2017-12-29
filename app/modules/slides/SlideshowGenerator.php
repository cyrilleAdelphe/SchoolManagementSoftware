<?php
class SlideshowGenerator
{
	public static function generateSlideshow()
	{

		$slider_html =  '<section id="slider" class=" swiper_wrapper full-screen clearfix" data-speed="1000" data-loop="true" data-autoplay="6000"><div class="slider-parallax-inner"><div class="swiper-container swiper-parent"><div class="swiper-wrapper">';
							    
		
		$slides = Slides::where('is_active','yes')
						->orderBy('slide_no')
						->get();

		$i=1;
		foreach($slides as $slide)
		{
			$dark = $slide->slider_class == 'dark' ? ' dark' : '';
			$slider_html .= '<div class="swiper-slide'.$dark.'" style="background-image: url('."'".Config::get('app.url').'app/modules/slides/asset/images/'.$slide['id'].'.jpg'."'".'); background-position: Center Top;';
				
			$slider_html .= '">';

			$center = $slide->in_center == 'yes' ? 'slider-caption-center' : '';
			$slider_html .= '<div class="container clearfix"><div class="slider-caption '.$center.'">';
			$slider_html .= '<h2 data-caption-animate="fadeInUp">'.$slide->title.'</h2>
                  <p data-caption-animate="fadeInUp" data-caption-delay="200">'.$slide->text.'</p></div></div></div>';
		}

		$slider_html .= '</div><div id="slider-arrow-left"><i class="icon-angle-left"></i></div><div id="slider-arrow-right"><i class="icon-angle-right"></i></div><div id="slide-number"><div id="slide-number-current"></div><span>/</span><div id="slide-number-total"></div></div></div></div></section>';
		
		

		return $slider_html;
	}
}