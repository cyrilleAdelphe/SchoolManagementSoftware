<div id="playlist">
	@foreach($playlist_items as $video)
		<li>
  		<a href="{{ URL::route('video-gallery-youtube-video', $video->snippet->resourceId->videoId) }}"> 
  			<img src="{{ $video->snippet->thumbnails->default->url }}">
  			<p>
  				{{ $video->snippet->title }}
  			</p>
  		</a>
  	</li>
	@endforeach
</div>
