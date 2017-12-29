@if(Session::has('init_tab'))
   @define $init_tab = Session::get('init_tab')
@endif


@extends('frontend.main')

@section('content')
  <div class="container">
      @if(isset($breadcrumb))
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-download"></i> &nbsp; Category List</a></li>
          @foreach($breadcrumb as $item)
            <li class="active">{{ $item }}</li>
          @endforeach
        </ol>
      @endif
  </div><!-- top ads ends -->
  <div class="container">
      <div class="row">
      <div class="col-xs-12 col-sm-3 col-md-3">  
        <div class="fileCat">
          <h3 class="modTitle">File Categories</h3>

            <section class="sidebar">
              {{ DownloadHelper::getCategoryTree() }}
            </section>

        </div>
        <!-- blog ends -->
      </div>
      
      <div class="col-xs-12 col-sm-9 col-md-9">
        <div class="row" style="margin:15px 0">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <form method = "get" action="{{URL::route('download-manager-frontend-file-search')}}">
              <div class="input-group input-group-sm">
                <input name="q" class="form-control" type="text" placeholder='search file'>
                <span class="input-group-btn">
                <button class="btn btn-info btn-flat" type="button">Go!</button>
                </span>
              </div>
            </form>
          </div>
        </div><!-- row ends -->
        
        @yield('download-frontend-content')
      </div>
    </div>
  </div>
@stop