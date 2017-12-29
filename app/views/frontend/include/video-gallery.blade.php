<?php 
$playlist_items = VideoGalleryHelperController::getYoutubePlaylistItems(YOUTUBE_KEY);
$video = end($playlist_items);
?>

@if($video)
<div class = "container">
	<div class="row">
		<div class="col-sm-7 col-md-8 lead-article">
		  	<div class="galTitle">Latest Video</div>
				<div class="row">
				
					<!-- category starts -->
					<div class="col-sm-4 catBox">
						<div class="galCat">
							<a href="{{ URL::route('video-gallery-youtube-video', $video->snippet->resourceId->videoId) }}"> 
								<img src="{{ $video->snippet->thumbnails->default->url }}">
								<p>
									{{ $video->snippet->title }}
								</p>
							</a>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>
@endif