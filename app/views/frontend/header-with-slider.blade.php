<?php
$school_logo_name = "";
if(File::exists(GENERAL_SETTINGS))
{
	$general_settings = json_decode(File::get(GENERAL_SETTINGS));
	$school_logo_name =  $general_settings->school_logo_name;
}

?>
<header id="header" class="transparent-header semi-transparent full-header">
			<div id="header-wrap">
				<div class="container clearfix">
					<div id="primary-menu-trigger"><i class="icon-reorder"></i></div>
					<!-- Logo
					============================================= -->
					<div id="logo">
						<a href="{{URL::route('home-frontend')}}" class="standard-logo" data-dark-logo="{{Config::get('app.url').'app/modules/settings/config/',$general_settings->school_logo_name}}" data-sticky-logo="{{Config::get('app.url').'app/modules/settings/config/',$general_settings->school_logo_name}}" data-mobile-logo="{{asset('sms/assets/img/logo-small.png')}}" ><img src = "{{Config::get('app.url').'app/modules/settings/config/',$general_settings->school_logo_name}}" alt="Logo"></a>



						<a href="{{URL::route('home-frontend')}}" class="retina-logo" data-dark-logo="{{Config::get('app.url').'app/modules/settings/config/',$general_settings->school_logo_name}}"><img src="{{Config::get('app.url').'app/modules/settings/config/',$general_settings->school_logo_name}}" alt="Logo"></a>

					</div><!-- #logo end -->



					<!-- Primary Navigation
					============================================= -->
					<nav id="primary-menu" class="style-3">
						{{ (new MenuHelper)->showMenu() }}
					</nav>
				</div>
			</div>
</header> <!-- header ends -->