<?php 
  //url for the tabs
  $tabs = array(
                array('url' => URL::route('student-list'),
                      'alias' => 'List'),

                array('url' => URL::route('student-create-get'),
                      'alias' => 'Create'),

                array('url' => URL::route('student-import-excel-get'),
                      'alias' => 'Import'),

                array('url' => URL::route('student-document-main'),
                      'alias' => 'Documents'),
                      
                array('url' => URL::route('student-migrate-students-get'),
                      'alias' => 'Migrate Students'),

                array('url' => URL::route('student-mass-roll-assignment-get'),
                      'alias' => 'Roll Assignment'),

                array('url' => URL::route('student-merge-parents-get'),
                      'alias' => 'Merge Parents'),

                array('url' => URL::route('student-merge-parents-get'),
                      'alias' => 'Merge Parents'),

                array('url' => URL::route('student-deactive-student-list-get'),
                      'alias' => 'Deactive Student List')
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    {{ucfirst(HelperController::dashToSpace($module_name))}} Manager
  </h1>
@stop

