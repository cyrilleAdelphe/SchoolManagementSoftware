@extends('frontend.main')

@section('custom-css')
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">		
	<link rel="stylesheet" href="{{ asset('/sms/assets/css/frontend/gallery.css') }}">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
@stop

@section('content')
	<br /><br /><br /><br /><br /><br /><br />
	<div class="container">
		<div class="col-sm-8">
			<div class="row">
				@foreach ($categories as $category)
					<!-- category starts -->
					<div class="col-sm-4 catBox">
						<div class="galCat">
							<a href="{{ URL::route('gallery-show-category', $category->id) }}">
								<div class="catImg">
									<img class="img-responsive" src="{{ $category->image }}" alt="title here" />
								</div>
								<div class="catTitle">
									{{ HelperController::limitWordCount($category->title, 27) }}
								</div>
							</a>
						</div>
					</div>
					<!-- category ends -->
				@endforeach

			</div>
		</div>
	</div>
@stop