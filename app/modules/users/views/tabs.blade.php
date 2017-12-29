<?php


$tabs = array(
                array('url'=>URL::route('users-list'), //this is for testimonial from students list
                      'alias'=>'Users')
                );



?>

{{-- the file extending this should define the $tabs as an array with each element array('url'=>...,'alias')--}}
@extends('backend.'.$role.'.main')

@section('tabs-header')
  <h1>
    {{ucfirst(HelperController::dashToSpace($module_name))}} Manager
  </h1>
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
       
@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop
