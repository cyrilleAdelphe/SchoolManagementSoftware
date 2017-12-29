<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('events-list'),
                      'alias'=>'List Events'),
                
                array('url'=>URL::route('events-create-get'),
                      'alias'=>'Create Event'),

                array('url'=>URL::route('events-calendar-get'),
                      'alias'=>'Calender'),
                
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Events Manager
  </h1>
@stop

