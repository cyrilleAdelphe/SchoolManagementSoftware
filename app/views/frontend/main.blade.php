<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="SemiColonWeb" />
    <title>{{json_decode(File::get(GENERAL_SETTINGS))->long_school_name}}</title>
    @yield('meta-info')


    <!-- Stylesheets
    ============================================= -->
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{asset('sms/assets/css/frontend/bootstrap.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('sms/assets/css/frontend/style.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('sms/assets/css/frontend/swiper.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('sms/assets/css/frontend/dark.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('sms/assets/css/frontend/font-icons.css')}}" type="text/css" />

    <link rel="stylesheet" href="{{asset('sms/assets/css/frontend/custom.css')}}" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--[if lt IE 9]>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->
    <script src="https://use.fontawesome.com/a89aa5c62e.js"></script>
    <script type="text/javascript" src="{{asset('sms/assets/js/frontend/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('sms/assets/js/frontend/plugins.js')}}"></script>
      
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    @yield('custom-css')

  </head>

  <body class="stretched no-transition">
    <input type = "hidden" id = "current_url" value = "{{URL::current()}}">
    
    <div id="wrapper" class="clearfix">
        
        @yield('content')

        @include('frontend.footer')        

    </div><!-- #wrapper end -->
    <!-- Go To Top
    ============================================= -->
    <div id="gotoTop" class="icon-angle-up"></div>
    <!-- Footer Scripts
    ============================================= -->
    <script type="text/javascript" src="{{asset('sms/assets/js/frontend/functions.js')}}"></script>
    <script>
    $(function()
    {

        $('nav ul').each(function()
        {
            var li = $(this).find('li');
            var flag = false;

            li.each(function()
            {
                var a = $(this).find('a');
                a.each(function()
                {
                    if($(this).attr('href') == $('#current_url').val())
                    {

                       flag = true;
                       return false;
                    }
                });

                if(flag)
                {
                    $(this).addClass('current');
                    flag = false;
                }

                if($(this).find('.current')[0])
                {
                    $(this).addClass('current');
                }   
            });
            
        });
    });
    </script>
    @yield('custom-js')
    
  </body>
</html>