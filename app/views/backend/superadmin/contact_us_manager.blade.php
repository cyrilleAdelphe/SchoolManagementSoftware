<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('contact-us-config-get'),
                      'alias'=>'Change recipient'),
                
                array('url'=>URL::route('contact-us-list'),
                      'alias'=>'Query List'),
                
                );
?>
@extends('backend.superadmin.main')

@section('content')
	
  <h4>
    Contact Us Manager
  </h4>
 
<!-- Main content -->

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

@stop