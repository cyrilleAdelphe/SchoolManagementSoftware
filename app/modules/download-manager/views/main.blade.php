@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
	<link href="{{ asset('sms/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
	<h1>
		Download Manager
	</h1>
@stop

@section('page-breadcrumb')
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Examples</a></li>
		<li class="active"></li>
	</ol>
@stop

@section('content')
	<!-- Main content -->
    <section class="content">

      <div class="box">
          <div class="box-body">
              @if(isset($parent))
                <h4>Detail view of <strong>{{ $parent['filename'] }}</strong></h4>
              <h6>{{ $parent['description'] }}</h6>
              @endif

              @if(isset($breadcrumb))
                <ol class="breadcrumb">
                  <li><a href="#"><i class="fa fa-download"></i> &nbsp; Category List</a></li>
                  @foreach($breadcrumb as $item)
                    <li class="active">{{ $item }}</li>
                  @endforeach
                </ol>
              @endif

              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li id="sub_categories_tab">
          					   <a href="{{ URL::route('download-manager-backend-subcategories',array($parent['id'],$parent['google_file_id'])) }}">Sub-Category List</a>
                    </li>

                    <li id="files_tab">
          					   <a href="{{ URL::route('download-manager-backend-files',array($parent['id'],$parent['google_file_id'])) }}">File List</a>
                    </li>

                    <li id="add_category_tab">
          					   <a href="{{ URL::route('download-manager-backend-add-category',array($parent['id'],$parent['google_file_id'])) }}">Add Category</a>
                    </li>

                    <li id="file_upload_tab">
          					   <a href="{{ URL::route('download-manager-backend-upload-files',array($parent['id'],$parent['google_file_id'])) }}">File Upload</a>
                    </li>

                    @if($current_user->role=='superadmin')
                      <li id="config_tab">
                         <a href="{{ URL::route('download-manager-config-get',array($parent['id'],$parent['google_file_id'])) }}">Frontend Config</a>
                      </li>
                    @endif
          			
                </ul>

            	@yield('tabs')
            </div>
        </div>
     </div>

@stop

@section('custom-js')
	
    <!-- FastClick -->
    <script src="{{ asset('sms/plugins/fastclick/fastclick.min.js') }}"></script>
    
    <!-- Page script -->
    <!-- DATA TABES SCRIPT -->
    <script src="{{ asset('sms/plugins/datatables/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('sms/plugins/datatables/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
      $(function () {
        $("#catList").dataTable();
        $("#fileList").dataTable();
        
      });
    </script>
    <!-- Editor SCRIPT -->
    {{--
    <script src="{{ asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
      $(function () {
        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();
      });
    </script>
    --}}

    @yield('download-manager-scripts')
@stop