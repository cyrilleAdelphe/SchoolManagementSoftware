@extends('download-manager.views.main')

@section('tabs')
	<div class="tab-content">
	    <div class="tab-pane active" id="tab_active">
	    	 <table id="fileList" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>SN</th>
                    <th>File Title</th>
                    <th>Description</th>
                    <th>Tags</th>
                    <th>Is Publishable</th>
                    <th>Is Featured</th>
                    <th>No. of downloads</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @define $i = 1
                  @foreach($children_files as $child_file)
                    <tr>
                      <td>{{ $i++ }}</td>
                      <td>{{ $child_file['filename'] }}</td>
                      <td>{{ $child_file['description'] }}</td>
                      <td>{{ $child_file['tags'] }}</td>
                      <td>{{ $child_file['is_active'] }}</td>
                      <td>{{ $child_file['is_featured'] }}</td>
                      <td>{{ $child_file['no_of_downloads'] }}</td>
                      <td>
                        @if($permissions['can_download'])
                          <a href="{{ $child_file['download_link'] }}">
                            <button data-toggle="tooltip" title="Download" class="btn btn-danger" type="button">
                              <i class="fa fa-fw fa-download"></i>
                            </button>
                          </a>
                        @endif

                        @if($permissions['can_edit'])
                          <a href="{{URL::route('download-manager-backend-edit-get', [$child_file['id'],$child_file['google_file_id']]) }}">
                            <button data-toggle="tooltip" title="Edit" class="btn btn-info" type="button">
                              <i class="fa fa-fw fa-edit"></i>
                            </button>
                          </a>
                        @endif

                        @if($permissions['can_delete'])
                          <a href="{{ $child_file['delete_link'] }}">
                            <button data-toggle="tooltip" title="Delete" class="btn btn-danger" type="button">
                              <i class="fa fa-fw fa-trash"></i>
                            </button>
                          </a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                    
                </tbody>
            </table>
	    </div>
	</div>
@stop

@section('download-manager-scripts')
		
	<script type="text/javascript">
      document.getElementById("files_tab").setAttribute("class", "active");
    </script>
    
@stop