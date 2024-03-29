<head>
	<meta charset="UTF-8" />
	<style>
		div {
		  margin-top: 3px;
		  padding: 0 10px;
		}

		button {
		  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
		  cursor: pointer;
		  font-weight: 700;
		  font-size: 13px;
		  padding: 8px 18px 10px;
		  line-height: 1;
		  color: #fff;
		  background: #345;
		  border: 0;
		  border-radius: 4px;
		  margin-left: 0.75em;
		}

		p {
		  display: inline-block;
		  margin-left: 10px;
		}
	</style>
</head>

<body>
	<script src="https://f.vimeocdn.com/js/froogaloop2.min.js"></script>
	<iframe id="player1" src="https://player.vimeo.com/video/{{$video_id}}?api=1&player_id=player1" width="630" height="354" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

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
	
	{{-- <div>
	  <button>Play</button>
	  <button>Pause</button>
	  <p>Status: <span class="status">&hellip;</span></p>
	</div> --}}

	<script>
		$(function() {
	    var iframe = $('#player1')[0];
	    var player = $f(iframe);
	    var status = $('.status');

	    // When the player is ready, add listeners for pause, finish, and playProgress
	    player.addEvent('ready', function() {
	        status.text('ready');
	        
	        player.addEvent('pause', onPause);
	        player.addEvent('finish', onFinish);
	        player.addEvent('playProgress', onPlayProgress);
	    });

	    // Call the API when a button is pressed
	    $('button').bind('click', function() {
	        player.api($(this).text().toLowerCase());
	    });

	    function onPause(id) {
	        status.text('paused');
	    }

	    function onFinish(id) {
	        status.text('finished');
	    }

	    function onPlayProgress(data, id) {
	        status.text(data.seconds + 's played');
	    }
		});
	</script>
</body>