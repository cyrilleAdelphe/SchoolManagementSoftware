@extends('gallery.views.tabs')

@section('tab-content')

	@foreach($albums as $a)
		<div class = "row">
			<a href = "{{URL::route('gallery-get-facebook-images')}}?facebook_album_id={{$a['id']}}&facebook_album_name={{$a['name']}}"><div class = "col-md-3">{{$a['name']}}</div>
			<div class = "col-md-3">{{$a['id']}}</div>
		</div>
	@endforeach

@stop