@extends('gallery.views.tabs')

@section('tab-content')
	<h1>{{$album_name}}</h1>
	@define $j = 0
	<form action = "{{URL::route('gallery-get-facebook-images-post')}}" method = "POST">
	@foreach($images as $i)
		<div class = "row">
			<div class = "col-md-9">
				<img class = "img-responsive" src = "{{end($i['images'])['source']}}">
				<input type = "hidden" name = "image_small[{{$i['id']}}]" value = "{{end($i['images'])['source']}}">
				<input type = "hidden" name = "image_large[{{$i['id']}}]" value = "{{$i['images'][0]['source']}}">
			</div>
			<div class = "col-md-3">
				<input type = "radio" name = "show[{{$i['id']}}]" value = "yes" @if($facebook['albums'][$album_id]['images'][$i['id']]['show'] == 'yes') checked @endif>Yes
				<input type = "radio" name = "show[{{$i['id']}}]" value = "no" @if($facebook['albums'][$album_id]['images'][$i['id']]['show'] == 'no') checked @endif>No
			</div>
		</div>
		<input type = "hidden" name = "image_id" value = "{{$i['id']}}">
		@define $j++
	@endforeach
	<input type = "hidden" name = "facebook_album_id" value = "{{$album_id}}">
	<input type = "hidden" name = "facebook_album_name" value = "{{$album_name}}">
	<input type = "submit">
	</form>

@stop