<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
    <link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">
    <link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />

    <!-- Morris.js charts -->
    @yield('custom-css')
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="skin-blue sidebar-mini">
    
        
        
              @yield('content')
        
        

    <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>    
    @yield('custom-js')
    
  </body>
</html>