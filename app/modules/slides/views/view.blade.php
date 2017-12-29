@extends('backend.superadmin.main')

@section('content')


	{{$actionButtons}}
	    
	<div class = 'form-group'>
		<label for = 'slide_no'  class = 'control-label'>Slide No.</label>
		<p>{{$data->slide_no}}</p>
	</div>

	<div class = 'form-group'>
		<label for = 'title'  class = 'control-label'>Title</label>
		<p>{{$data->title}}</p>
	</div>

	<div class = 'form-group'>
		<label for = 'text'  class = 'control-label'>Text</label>
		<p>{{$data->Text}}</p>
	</div>

	<div class = 'form-group'>
		<label for = 'link'  class = 'control-label'>Link</label>
		<p>{{$data->link}}</p>
	</div>

	<div class = 'form-group'>
		<label for = 'profile_pic'  class = 'control-label'>Image</label>
		<img src = "{{Config::get('app.url').'app/modules/slides/asset/images/'.$data->id.'.jpg'}}">
	</div>
@stop


