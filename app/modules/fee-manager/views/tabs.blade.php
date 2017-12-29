<?php 
  //url for the tabs
  $tabs = array(
    array('url'=>URL::route('fee-manager-monthly-fee-get'),
          'alias'=>'Monthly Fee'),

    array('url'=>URL::route('fee-manager-hostel-fee-get'),
          'alias'=>'Hostel Fee'),

    array('url'=>URL::route('fee-manager-examination-fee-get'),
          'alias'=>'Examination Fee'),

    array('url'=>URL::route('fee-manager-misc-class-fee-get'),
          'alias'=>'Miscallaneous Class fee'),

    array('url'=>URL::route('fee-manager-misc-student-fee-get'),
          'alias'=>'Miscallaneous Student fee'),

    array('url'=>URL::route('fee-manager-scholarship-get'),
          'alias'=>'Scholarships'),

    array('url'=>URL::route('fee-manager-tax-config-get'),
          'alias'=>'Taxes'),
  );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Fee Manager
  </h1>
@stop

