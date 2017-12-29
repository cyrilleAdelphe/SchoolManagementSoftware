@extends('download-manager.views.frontend.template')

@section('download-frontend-content')
  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li @if(!isset($init_tab) || $init_tab=='latest') class="active" @endif>
        <a href="#tab_1" data-toggle="tab">Latest Downloads</a>
      </li>
      {{-- <li><a href="#tab_2" data-toggle="tab">Featured Files</a></li>
      <li><a href="#tab_3" data-toggle="tab">Most Popular</a></li> --}}
      
    </ul>
    <div class="tab-content">
      <div class="tab-pane @if(!isset($init_tab) || $init_tab=='latest') active @endif" id="tab_1">
                
        @foreach($recent_files as $file)
          <div class="downloadListBox">
            <div class="row">
              <div class="hidden-xs col-sm-2 col-md-2 dIcon">
                {{DownloadHelper::getFileIcon($file['mime_type'])}}
              </div>
              <div class="col-xs-12 col-sm-10 col-md-10">
                <div class="subTitle">{{$file['filename']}}</div>
                <div class="text-light-blue"><small>Uploaded at {{$file['created_at']}} by {{$file['created_by']}}</small></div>
                {{$file['description']}}
                <div class="actions">
                    <a href="{{DownloadHelper::getDownloadLink($file)}}">
                      <button class="btn btn-success btn-xs">
                        <i class="fa fa-fw fa-download"></i> &nbsp;Download
                      </button>&nbsp;
                    </a>
                    <button class="btn btn-primary btn-xs disabled">Hits ({{$file['no_of_downloads']}})</button>
                </div>
              </div>
            </div>
          </div><!-- download 1 ends -->
        @endforeach
        @if(sizeof($children_files))
          {{$files_paginated->links()}}
        @endif
      </div>
      @define $file_count=0
      <div class="tab-pane" id="tab_2">
       
        @foreach($featured_files as $file)
          @if($file['is_featured']=='no')
            <?php continue; ?>
          @endif
          @define $file_count++
          <div class="downloadListBox">
            <div class="row">
              <div class="hidden-xs col-sm-2 col-md-2 dIcon">
                {{DownloadHelper::getFileIcon($file['mime_type'])}}
              </div>
              <div class="col-xs-12 col-sm-10 col-md-10">
                <div class="subTitle">{{$file['filename']}}</div>
                <div class="text-light-blue"><small>Uploaded at {{$file['created_at']}} by {{$file['created_by']}}</small></div>
                {{$file['description']}}
                <div class="actions">
                    <a href="{{DownloadHelper::getDownloadLink($file)}}">
                      <button class="btn btn-success btn-xs">
                        <i class="fa fa-fw fa-download"></i> &nbsp;Download
                      </button>&nbsp;
                    </a>
                    <button class="btn btn-primary btn-xs disabled">Hits ({{$file['no_of_downloads']}})</button>
                </div>
              </div>
            </div>
          </div><!-- download 1 ends -->
        @endforeach
        @if($file_count)
          {{$featured_paginated->links()}}
        @endif
      </div>
      
      <div class="tab-pane" id="tab_3">
        @foreach($most_downloaded as $file)
          <div class="downloadListBox">
            <div class="row">
              <div class="hidden-xs col-sm-2 col-md-2 dIcon">
                {{DownloadHelper::getFileIcon($file['mime_type'])}}
              </div>
              <div class="col-xs-12 col-sm-10 col-md-10">
                <div class="subTitle">{{$file['filename']}}</div>
                <div class="text-light-blue"><small>Uploaded at {{$file['created_at']}} by {{$file['created_by']}}</small></div>
                {{$file['description']}}
                <div class="actions">
                    <a href="{{DownloadHelper::getDownloadLink($file)}}">
                      <button class="btn btn-success btn-xs">
                        <i class="fa fa-fw fa-download"></i> &nbsp;Download
                      </button>&nbsp;
                    </a>
                    <button class="btn btn-primary btn-xs disabled">Hits ({{$file['no_of_downloads']}})</button>
                </div>
              </div>
            </div>
          </div><!-- download 1 ends -->
        @endforeach
        @if(sizeof($most_downloaded_paginated))
          {{$most_downloaded_paginated->links()}}
        @endif
      </div>
    </div><!-- tab-content ends -->
  </div><!-- nav-tabs-custom ends-->
@stop

