<header id="header" class="full-header">
	<div id="header-wrap">
		<div class="container clearfix">
			<div id="primary-menu-trigger"><i class="icon-reorder"></i></div>
			<!-- Logo
			============================================= -->
			<div id="logo">
						<a href="{{URL::route('home-frontend')}}" class="standard-logo" data-dark-logo="{{asset('sms/assets/img/logo.png')}}" data-sticky-logo="{{asset('sms/assets/img/logo2.png')}}" data-mobile-logo="{{asset('sms/assets/img/logo-small.png')}}" ><img src="{{asset('sms/assets/img/logo.png')}}" alt="Logo"></a>

						<a href="{{URL::route('home-frontend')}}" class="retina-logo" data-dark-logo="{{asset('sms/assets/img/logo-small.png')}}"><img src="{{asset('sms/assets/img/logo-small.png')}}" alt="Logo"></a>

					</div><!-- #logo end -->



			<!-- Primary Navigation
			============================================= -->
			<nav id="primary-menu" class="style-3">
				{{ (new MenuHelper)->showMenu() }}
			</nav>
		</div>
	</div>
</header> <!-- header ends -->