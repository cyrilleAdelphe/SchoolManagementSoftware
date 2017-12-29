@extends('frontend.main')

@section('custom-css')
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
@stop

@section('content')
	{{-- <iframe width="854" height="480" src="https://www.youtube.com/embed/videoseries?list=PLMKA5kzkfqk18faruyGLVu1YI1-4i2N7P&amp;hl=en_US&showinfo=1" frameborder="0" allowfullscreen></iframe> --}}
	@include('video-gallery.views.youtube-playlist')
@stop