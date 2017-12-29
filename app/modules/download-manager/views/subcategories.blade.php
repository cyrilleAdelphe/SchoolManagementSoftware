@define $no_of_files_buffer = Session::get('no_of_files_buffer')

@extends('download-manager.views.main')

@section('tabs')
	<div class="tab-content">
	    <div class="tab-pane active" id="tab_active">
	      <table id="catList" class="table table-bordered table-striped">
	            <thead>
	              <tr>
	                <th>SN</th>
	                <th>Sub-category Title</th>
	                <th>Description</th>
	                <th>No. of Subcategories</th>
	                <th>No. of Files</th>
	                <th>Action</th>
	              </tr>
	            </thead>
	            <tbody>
	              
	              @define $s_no = 1
	              @foreach($children_folder as $child_folder)
	                  <tr>
	                    <td>{{ $s_no++ }}</td>  
	                    <td>{{ $child_folder['filename'] }}</td>
	                    <td>{{ $child_folder['description'] }}</td>
	                    <td>{{ $no_of_files_buffer[$child_folder['id']]['no_of_folders'] }}</td>
	                    <td>{{ $no_of_files_buffer[$child_folder['id']]['no_of_files'] }}</td>

	                    {{--
	                    <td>{{ $child_folder['no_of_subcategories'] }}</td>
	                    <td>{{ $child_folder['no_of_files'] }}</td>
	                    --}}
	                    
	                    <td>
	                    @if($permissions['can_view'])
	                      <a href = "{{ $child_folder['redirect'] }}">
	                        <button data-toggle="tooltip" title="View Detail" class="btn btn-success" type="button">
	                          <i class="fa fa-fw fa-list-ul"></i>
	                        </button>
	                        </a>
	                    @endif

	                    @if($permissions['can_edit'])
	                      <a href = "{{URL::route('download-manager-backend-edit-get', [$child_folder['id'],$child_folder['google_file_id']]) }}">
	                        <button data-toggle="tooltip" title="Edit" class="btn btn-info" type="button">
	                          <i class="fa fa-fw fa-edit"></i>
	                        </button>
	                      </a>
	                    @endif

	                    @if($permissions['can_delete'])
	                      @if(isset($child_folder['delete_link']))
	                        <a href = "{{ $child_folder['delete_link'] }}">
	                          <button data-toggle="tooltip" title="Delete" class="btn btn-danger" type="button">
	                            <i class="fa fa-fw fa-trash"></i>
	                          </button>
	                        </a>
	                      @endif
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
	{{-- This script is tab specific --}}
	
	<script type="text/javascript">
      document.getElementById("sub_categories_tab").setAttribute("class", "active");
    </script>
    
@stop