<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('pdr-list'),
                      'alias'=>'List View'),
                
                array('url'=>URL::route('pdr-create-get'),
                      'alias'=>'Add New daily update')
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Daily Study Progress Manager
  </h1>
@stop
