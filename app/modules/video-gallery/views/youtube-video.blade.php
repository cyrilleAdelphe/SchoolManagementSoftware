@extends('frontend.main')

@section('custom-css')
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
@stop

@section('content')
	<iframe width="854" height="480" src="https://www.youtube.com/embed/{{ $video_id }}" frameborder="0" allowfullscreen></iframe>
	@include('video-gallery.views.youtube-playlist')
@stop