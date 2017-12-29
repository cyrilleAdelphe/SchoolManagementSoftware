<?php 
  //url for the tabs
  $tabs = array(
                array('url'   =>  URL::route('attendance-create-get'),
                      'alias' =>  'Daily Attendance',
                      'id'    =>  'tab_daily_attendance'),
                
                array('url'   =>  URL::route('attendance-view-class-section-history'),
                      'alias' =>  'Attendance History',
                      'id'    =>  'tab_attendance_history')
                
                );
?>
@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>
    Attendance
    <small>View attendance details.</small>
  </h1>
  
</section>
@stop

@section('content')
	
  
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
