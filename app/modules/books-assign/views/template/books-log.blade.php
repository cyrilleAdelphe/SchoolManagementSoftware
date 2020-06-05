<?php 
  //url for the tabs
  $tabs = array(
                array('url'   =>  URL::route('books-list'),
                      'alias' =>  'View Books',
                      'id'    =>  'tab_view_books'),
                
                array('url'   =>  URL::route('books-assign-create-get'),
                      'alias' =>  'Assign Books',
                      'id'    =>  'tab_assign_books'),

                array('url'   =>  URL::route('books-assign-list'),
                      'alias' =>  'Assigned History',
                      'id'    =>  'tab_assigned_history'),

                array('url'   =>  URL::route('book-categories-list'),
                      'alias' =>  'Categories',
                      'id'    =>  'tab_categories'),

                array('url'   =>  URL::route('generete-bar-code-get'),
                      'alias' =>  'Generate Bar Code',
                      'id'    =>  'tab_barcodes'),
                
                );
?>
@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>
    Books Log
    <small>View books detail. Assign or update books status.</small>
  </h1>
  
</section>
@stop

@section('content')
	
  
<!-- Main content -->

        <div class="nav-tabs-custom">            
          <ul class="nav nav-tabs">
            @foreach($tabs as $tab)
              <li @if(Request::url() == $tab['url']) {{'class="active"'}} @endif><a href="{{$tab['url']}}">{{$tab['alias']}}</a></li>
            @endforeach
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_active">
              @yield('tab-content')
            </div>
          </div>
        </div>

@stop
