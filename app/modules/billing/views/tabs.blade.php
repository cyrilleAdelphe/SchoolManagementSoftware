<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('billing-create-fee-get'),
                      'alias'=>'Create Fee'),

                array('url'=>URL::route('billing-generate-fee-get'),
                      'alias'=>'Generate Fee'),

                array('url'=>URL::route('billing-direct-invoice-get'),
                      'alias'=>'Direct Invoice'),

                array('url'=>URL::route('billing-income-report'),
                      'alias'=>'Income Report'),
                               
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Account
  </h1>
@stop
