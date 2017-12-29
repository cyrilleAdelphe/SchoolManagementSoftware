<?php


  

if(Auth::superadmin()->check())
{
	$tabs = array(
    array(
      'url' => URL::route('staff-request-staffs-history-list'), //this is for staffed history
      'alias' => 'Staff History'
    ),
    array(
      'url' => URL::route('staff-request-create-others-request'),
      'alias' => 'Create other\'s request'
    )
  );
}
else
{
  $tabs = array(
    array('url'=>URL::route('staff-request-list'), //this is for message from students list
          'alias'=>'Requests'),

    array('url'=>URL::route('staff-request-create-get'), //this is for message from guardians list
          'alias'=>'Create Request')
  ); 
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
