<body>
	<h1>Vimeo Simple API Gallery Example</h1>
	
	<div id="wrapper">
		<div id="embed"></div>
		<div id="thumbs">
			<ul>
			@foreach ($videos as $video)
				<li>
					<a href="{{ URL::route('video-gallery-show-video', $video->id) }}">
						<img src="{{ $video->thumbnail_medium }}" class="thumb" />
						<p>{{ $video->title }}</p>
					</a>
				</li>
			@endforeach
			</ul>
		</div>
	</div>

</body>