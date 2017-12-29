<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route($module_name.'-list'),
                      'alias'=>'List'),

                array('url'=>URL::route($module_name.'-create-get'),
                      'alias'=>'Create'),
                               
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    {{ucfirst(HelperController::dashToSpace($module_name))}} Manager
  </h1>
@stop

