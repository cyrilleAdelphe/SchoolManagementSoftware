<?php

$data = Testimonial::where('is_active', 'yes')
                    ->where('show_in_module', 'yes')
                    ->select(array('content', 'id'))
                    ->orderBy('sort_order', 'ASC')
                    ->orderBy('updated_at', 'DESC')
                    ->take(JsonConfigurationController::getFieldValue('testimonial', 'number_of_testimonials_to_show', 'no_of_displays'))
                    ->get();

?>

<div class="light-grey-box"> 
      <div class="container hidden-xs testimonial">
          <div class="lead central-title">What people say about us</div>
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
              <div class="carousel-inner">
              @foreach($data as $index => $d)
              <div class="item @if($index == 0) active @endif">
                  <img src="{{Config::get('app.url').'app/modules/testimonial/asset/images/'.$d->id.'.jpg'}}"  class="center img-circle img-responsive" width="70" height="70">
                  <p>
                    {{$d->content}}
                  </p>
                </div>
              @endforeach
              <div class="clear"></div>
                
              </div>
            </div>
      </div><!-- testimonials ends here -->
    </div>