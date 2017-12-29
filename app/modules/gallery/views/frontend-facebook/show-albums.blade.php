@extends('frontend.main')

@section('content')
	@if(count($albums))
		@foreach($albums as $album)
			<p><a href = "{{URL::route('frontend-gallery-facebook-show-albums', $album['id'])}}"><b>{{$album['name']}}</b> <img src = "{{$album['image']}}"></a></p>
		@endforeach
	@else
		<b>No album found</b>
	@endif
@stop