@extends('download-manager.views.frontend.template')

@section('download-frontend-content')
  @if(!count($query_files))
    <h3>No file found</h3>
  @endif
	@foreach($query_files as $file)
      <div class="downloadListBox">
        <div class="row">
          <div class="hidden-xs col-sm-2 col-md-2 dIcon">
            {{DownloadHelper::getFileIcon($file->mime_type)}}
          </div>
          <div class="col-xs-12 col-sm-10 col-md-10">
            <div class="subTitle">{{$file->filename}}</div>
            <div class="text-light-blue"><small>Uploaded at {{$file->created_at}} by {{$file->created_by}}</small></div>
            {{$file->description}}
            <div class="actions">
                <a href="{{URL::route('download-manager-backend-file-download',[$file->id,$file->google_file_id ])}}">
                  <button class="btn btn-success btn-xs">
                    <i class="fa fa-fw fa-download"></i> &nbsp;Download
                  </button>&nbsp;
                </a>
                <button class="btn btn-primary btn-xs disabled">Hits ({{$file->no_of_downloads}})</button>
            </div>
          </div>
        </div>
      </div><!-- download 1 ends -->
    @endforeach
@stop