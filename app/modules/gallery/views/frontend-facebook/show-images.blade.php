@extends('frontend.main')

@section('content')
	<h1>{{$album_name}}</h1>

	@foreach($images as $img)
	<p><img src = "{{$img['image_small']}}"><img src = "{{$img['image_large']}}"></p>
	@endforeach
@stop