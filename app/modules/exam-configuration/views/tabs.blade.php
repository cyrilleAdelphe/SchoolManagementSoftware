<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('exam-configuration-list'),
                      'alias'=>'Exams'),
                
                array('url'=>URL::route('exam-configuration-create-get'),
                      'alias'=>'Add New Exam'),
                array('url'=>URL::route('exam-details-view-routine').'?'.'exam_id='.Input::get('exam_id', 0).'&class_id='.Input::get('class_id', 0).'&section_id='.Input::get('section_id', 0),
                      'alias'=>'Routine')
                               
                );
?>

@extends('backend.'.$role.'.tabs')

@section('custom-css')
  <link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}">
@stop
@section('tabs-header')
  <h1>
    Exams Manager
  </h1>
@stop
