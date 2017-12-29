@extends('backend.'. $role . '.main')

@section('content')

	<form method="post" action="{{URL::route('video-gallery-config-post')}}">
		<div class="form-group @if($errors->has("vimeo_user_id")) {{"has-error"}} @endif">
			<label> Vimeo User Id </label>
			<input name="vimeo_user_id" value="{{ $config['vimeo_user_id'] }}" />
			<span class = "form-error">
	      @if($errors->has('vimeo_user_id')) 
	        {{ $errors->first('vimeo_user_id') }} 
	      @endif
	    </span>
		</div>

		<div class="form-group @if($errors->has("youtube_playlist_id")) {{"has-error"}} @endif">
			<label> Youtube Playlist Id </label>
			<input name="youtube_playlist_id" value="{{ $config['youtube_playlist_id'] }}" />
			<span class = "form-error">
	      @if($errors->has('youtube_playlist_id')) 
	        {{ $errors->first('youtube_playlist_id') }} 
	      @endif
	    </span>
		</div>

		{{Form::token()}}

	  <div class="form-group">
	    <button class="btn btn-primary" type="submit">Submit</button>
	  </div>
	</form>
@stop