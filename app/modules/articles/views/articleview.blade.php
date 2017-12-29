@extends('frontend.main')

@section('sub-title') | {{$article['title']}}@stop

@section('meta-info')
  <meta name="description" content="{{$article['meta_description']}}">
  <meta name="keywords" content="{{$article['meta_tag']}}">
@stop

@section('content')
@include('frontend.header-without-slider')
@yield('page-header')
  <div class="container mainContent">
    <div class="row">
      <div class="col-sm-7 col-md-8 lead-article">
        <h1>{{$article['title']}}</h1>
        <p>
          @define $user = Users::where('username',$article['created_by'])->first()
          @if($user)
            Posted By: <a href="{{URL::route('users-view-profile',$user['id']) }}">{{$user['name']}}</a>
          @endif
        </p>
        {{$article['content']}}
      </div>
    </div>
  </div>

@stop
          

@section('custom-js')  
    <!-- Page script -->
    <script>
      (function($){
          $(document).ready(function(){
            $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
              event.preventDefault(); 
              event.stopPropagation(); 
              $(this).parent().siblings().removeClass('open');
              $(this).parent().toggleClass('open');
            });
          });
        })(jQuery);
    </script>
@stop
    
  