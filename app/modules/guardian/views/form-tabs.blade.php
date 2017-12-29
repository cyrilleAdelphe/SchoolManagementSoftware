<?php 
  //url for the tabs
  $tabs = array(
    array('url' => URL::route('guardian-list'),
          'alias' => 'List'),

    array('url' => URL::route('guardian-create-get'),
          'alias' => 'Create'),

    array('url' => URL::route('guardian-import-excel-get'),
          'alias' => 'Import'),

  );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    {{ucfirst(HelperController::dashToSpace($module_name))}} Manager
  </h1>
@stop

