@extends('testimonial.views.tabs')

@section('tab-content')

<div class = 'form-group'>
			<label for = 'content'  class = 'control-label'>Content :</label>
			<p>{{$data->content}}</p>
		</div>

		<div class = 'form-group'>
			<label for = 'sort_order'  class = 'control-label'>Sort Order :</label>
			<p>{{$data->sort_order}}</p>
		</div>

		<div class = 'form-group'>
			<img src = "{{Config::get('app.url').'app/modules/testimonial/asset/images/'.$data->id.'.jpg'}}">
		</div>

		<div class = "form-group">
			<label for = "show_in_module">Show In Module:</label>
			<p>{{$data->show_in_module}}</p>
		</div>

		<div class = "form-group">
			<label for = "is_active">Is Active:</label>
			<p>{{$data->is_active}}</p>
		</div>
@stop