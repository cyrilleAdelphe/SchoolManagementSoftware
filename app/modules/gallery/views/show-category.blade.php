@extends('frontend.main')

@section('custom-css')
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{ asset('/sms/assets/css/frontend/blueimp-gallery.css') }}">
  <link rel="stylesheet" href="{{ asset('/sms/assets/css/frontend/gallery.css') }}">
@stop

@section('content')
	<br /><br /><br /><br />
	<div class="container">
		<div class="row">
		  <div class="col-sm-8">

				<div id="blueimp-gallery" class="blueimp-gallery" data-use-bootstrap-modal="false">
	        <!-- The container for the modal slides -->
	        <div class="slides"></div>
	        <!-- Controls for the borderless lightbox -->
	        <h3 class="title"></h3>
	        <a class="prev">‹</a>
	        <a class="next">›</a>
	        <a class="close">×</a>
	        <a class="play-pause"></a>
	        <ol class="indicator"></ol>                        
	      </div>

	      <div class="galTitle">{{ $category->title }}</div>
	      <div class="row">
	      	@foreach ($images as $image)
	          <div class="col-sm-3 galImg">
              <a href="{{ Config::get('app.url') . 'app/modules/gallery/assets/images/original/' . $image->id }}" title="Image name" data-gallery >
								<img class="img-responsive" src="{{ Config::get('app.url') . 'app/modules/gallery/assets/images/thumbnails/' . $image->id }}" alt="Image name">
              </a>
	          </div>
	        @endforeach
	      </div>
		  </div> <!-- col-row-8 ends -->
		</div>
	</div>
@stop

@section('custom-js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-gallery/2.21.2/js/jquery.blueimp-gallery.min.js"></script>
@stop