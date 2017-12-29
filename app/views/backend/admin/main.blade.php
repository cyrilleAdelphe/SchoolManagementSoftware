{{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
@define $employee_name = Employee::where('id', Auth::admin()->user()->admin_details_id)->pluck('employee_name')

@define $photo = Employee::where('id', Auth::admin()->user()->admin_details_id)->pluck('photo')
{{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
    <!-- FontAwesome 4.3.0 -->
    <link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="{{asset('sms/assets/css/ionicons.min.css')}}" rel="stylesheet" type="text/css" />    
    <!-- Theme style -->
    <link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />

    <!-- bootstrap wysihtml5 - text editor -->
    <link href="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- jQuery 2.1.4 -->
    <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>    
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
    <div class="wrapper">
      
      @include('backend.admin.header')
      <!-- Left side column. contains the logo and sidebar -->
      
      @include('backend.admin.sidebar1')

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
         
          <!-- Left side column. contains the logo and sidebar -->
          @yield('tabs-header')
          
          @if(Session::has('error-msg'))
          <div class = "box-body">
            
            <div class = "alert alert-danger alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('error-msg'))}}">
                {{ Session::get('error-msg') }}
            </div>            
        </div>
        @endif
        @if(Session::has('success-msg'))
        <div class = "box-body">           
            <div class = "alert alert-success alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('success-msg'))}}">
                {{ Session::get('success-msg') }}
            </div>            
        </div>
        @endif
        @if(Session::has('warning-msg'))
        <div class = "box-body">           
            <div class = "alert alert-info alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('info-msg'))}}">
                {{ Session::get('warning-msg') }}
            </div>           
        </div>
         @endif
        @yield('page-header')
        </section>
        <section class="content">
          <div class="box">
            <div class="box-body">
              @yield('content')
            </div>
          </div>
        </section>
        <input type = "hidden" id = "current_url" value = "{{URL::current()}}">
      </div><!-- /.content-wrapper -->
     
     @include('backend.admin.footer')
    </div><!-- ./wrapper -->

    <script src="{{asset('sms/assets/js/app.min.js')}}" type="text/javascript"></script>    
    
    <script src="{{asset('backend-js/remove-global.js')}}" type="text/javascript"></script>  

    <script src="{{asset('backend-js/sidebarActivate.js')}}" type="text/javascript"></script>  

    @yield('custom-js')
    
  </body>
</html>