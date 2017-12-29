<?php 
  //url for the tabs
  $tabs = array(
                array('url' => URL::route('academic-session-list'),
                      'alias' => 'List'),

                array('url' => URL::route('academic-session-create-get'),
                      'alias' => 'Create'),

                array('url' => URL::route('academic-session-migrate-session-get'),
                      'alias' => 'Migrate Session'),
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    {{ucfirst(HelperController::dashToSpace($module_name))}} Manager
  </h1>
@stop

