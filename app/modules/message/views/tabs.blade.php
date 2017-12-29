<?php


$tabs = array(
  array('url'=>URL::route('message-list'), //this is for message from students list
        'alias'=>'Messages'),

  array('url'=>URL::route('notification-list'), //this is for message from guardians list
        'alias'=>'Notifications'),

  array('url'=>URL::route('message-create-get'), //this is for message from guardians list
        'alias'=>'Create Message')
                 
);

if(Auth::superadmin()->check())
{
	$tabs[] = array('url'=>URL::route('message-staffs-history-list'), //this is for staffed history
                      'alias'=>'Staff History');
}

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
