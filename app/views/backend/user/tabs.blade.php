{{-- the file extending this should define the $tabs as an array with each element array('url'=>...,'alias')--}}
@extends('backend.'.$role.'.main')

@section('content')
	
  @yield('tabs-header')
  
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active"></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="box">
      <div class="box-body">
        <div class="nav-tabs-custom">            
          <ul class="nav nav-tabs">
            @foreach($tabs as $tab)
              <li @if(Request::url() == $tab['url']) {{'class="active"'}} @endif><a href="{{$tab['url']}}">{{$tab['alias']}}</a></li>
            @endforeach
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_active">
              @yield('tab-content')
            </div>
          </div>
        </div>
      </div>
  </div>
</section>
@stop
