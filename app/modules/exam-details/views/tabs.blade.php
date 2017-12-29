<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('exam-details-list'),//.'?'.'exam_id='.Input::get('exam_id', 0).'&class_id='.Input::get('class_id', 0).'&section_id='.Input::get('section_id', 0),
                      'alias'=>'Create routine'),
                array('url'=>URL::route('exam-details-view-routine'),//.'?'.'exam_id='.Input::get('exam_id', 0).'&class_id='.Input::get('class_id', 0).'&section_id='.Input::get('section_id', 0),
                      'alias'=>'View Routine'),
                array('url'=>URL::route('exam-configuration-list'),//.'?'.'exam_id='.Input::get('exam_id', 0).'&class_id='.Input::get('class_id', 0).'&section_id='.Input::get('section_id', 0),
                      'alias'=>'View Exams'),
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Exam's Routine Manager
  </h1>
@stop

