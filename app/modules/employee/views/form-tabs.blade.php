<?php 
  //url for the tabs
  $tabs = array(
    array('url' => URL::route('employee-list'),
          'alias' => 'List'),

    array('url' => URL::route('employee-create-get'),
          'alias' => 'Create'),

    array('url' => URL::route('employee-document-main'),
          'alias' => 'Documents'),
                 
  );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    {{ucfirst(HelperController::dashToSpace($module_name))}} Manager
  </h1>
@stop

