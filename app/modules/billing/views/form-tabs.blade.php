<?php 
  //url for the tabs
  $tabs = array(
                /*array('url' => URL::route('billing-fee-print-get'),
                      'alias' => 'Fee Print'),*/

                array('url' => URL::route('billing-list-view-fee-print-get'),
                      'alias' => 'List View'),
                 /*array('url' => URL::route('direct-invoice-tab-get'),
                      'alias' => 'Direct Invoice'),
                 array('url' => URL::route('opening-balance-tab-get'),
                      'alias' => 'Opening Balance'),*/

                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    {{ucfirst(HelperController::dashToSpace($module_name))}} Manager
  </h1>
@stop

