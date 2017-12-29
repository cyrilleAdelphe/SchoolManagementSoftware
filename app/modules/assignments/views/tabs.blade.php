<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('assignments-files'),
                      'alias'=>'Files'),

                array('url'=>URL::route('assignments-upload-get'),
                      'alias'=>'Upload file'),

                array('url'=>URL::route('assignments-config-get'),
                      'alias'=>'Configuration'),
                               
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Study Materials
  </h1>
@stop

