<?php 
  //url for the tabs
  $tabs = array(
                array('url'=>URL::route('gallery-list'),
                      'alias'=>'List Images'),

                array('url'=>URL::route('gallery-create-get'),
                      'alias'=>'Add Images'),
                
                array('url'=>URL::route('gallery-category-list'),
                      'alias'=>'List Categories'),

                array('url'=>URL::route('gallery-category-create-get'),
                      'alias'=>'Create Category'),

                array('url'=>URL::route('gallery-get-facebook-albums'),
                      'alias'=>'Facebook Albums'),
                               
                );
?>

@extends('backend.'.$role.'.tabs')

@section('tabs-header')
  <h1>
    Gallery
  </h1>
@stop

