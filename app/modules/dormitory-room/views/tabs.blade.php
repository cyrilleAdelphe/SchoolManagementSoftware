<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('dormitory-student-list'),
                      'alias'=>'List Students'),

                array('url'=>URL::route('dormitory-student-create-get'),
                      'alias'=>'Add Student'),
                
                array('url'=>URL::route('dormitory-room-list'),
                      'alias'=>'List dormitories'),

                array('url'=>URL::route('dormitory-room-create-get'),
                      'alias'=>'Add dormitory'),
                               
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Dormitory
  </h1>
@stop

