<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('subject-list'),
                      'alias'=>'<i class="fa fa-fw fa-list"></i> Subjects list'),
                
                array('url'=>URL::route('subject-create-get'),
                      'alias'=>'<i class="fa fa-fw fa-edit"></i> Add New'),

                array('url'=>URL::route('subject-teacher-create-get'),
                      'alias'=>'<i class="fa fa-fw fa-new"></i> Assign Teacher'),
                               
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Subjects Manager
  </h1>
@stop

