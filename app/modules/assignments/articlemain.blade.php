<!-- 
	The define requires extension to the Blade class set in app/start/global.php
	Reference: http://stackoverflow.com/questions/13002626/laravels-blade-how-can-i-set-variables-in-a-template
	BTW: you can do <?php ?> too. But this way is supposedly not too 'elegant' for a blade template
-->
@define $init_tab = Session::get('init_tab') 

@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
	<!-- Theme style -->    
    <link href= "{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" type="text/css" />
     <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')    
  <h1>Article Manager</h1>
@stop

@section('content')

	<!-- Main content -->
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
              <li {{ !$init_tab || $init_tab == 'create_article' ? 'class="active"' : '' }}><a href="#tab_1" data-toggle="tab">Create Article</a></li>
              <li {{ $init_tab == 'list_article' ? 'class="active"' : ''}}><a href="#tab_2" data-toggle="tab">Article List</a></li>
              <li {{ $init_tab == 'add_category' ? 'class="active"' : ''}}><a href="#tab_3" data-toggle="tab">Create Category</a></li>
              <li {{ $init_tab == 'list_category' ? 'class="active"' : ''}}><a href="#tab_4" data-toggle="tab">Category List</a></li>
              <li {{ $init_tab == 'featured_category_manager' ? 'class="active"' : ''}}><a href="#tab_6" data-toggle="tab">Category manager</a></li>
          </ul>
          <div class="tab-content">
            <div class="{{ !$init_tab || $init_tab == 'create_article' ? 'tab-pane active' : 'tab-pane' }}"
            		id="tab_1">
	            @include('articles.views.articlecreate')
            </div>

            <div class="{{ $init_tab == 'list_article' ? 'tab-pane active' : 'tab-pane' }}"
            		id="tab_2">
              @include('articles.views.articlelist')
            </div>   

              <div class="{{ $init_tab == 'add_category' ? 'tab-pane active' : 'tab-pane' }}"
              		id="tab_3">                       
              	@include('articles.views.articleaddcategory')
              </div>
              
              <div class="{{ $init_tab == 'list_category' ? 'tab-pane active' : 'tab-pane' }}"
              		id="tab_4">
              	<div class="box-header">
                    <h3 class="box-title">Category list</h3>
                </div><!-- /.box-header -->
                @include('articles.views.articlelistcategory')
              </div>

              <div class="{{ $init_tab == 'featured_article_manager' ? 'tab-pane active' : 'tab-pane' }}"
              		id="tab_5">
              	<div class="box-header">
                    <h3 class="box-title">Featured Article Manager</h3>
                </div><!-- /.box-header -->
                @include('articles.views.articleconfig')
              </div>

              <div class="{{ $init_tab == 'featured_category_manager' ? 'tab-pane active' : 'tab-pane' }}"
              		id="tab_6">

                @include('articles.views.articlecategoryconfig')
              </div>



            </div>
        </div>

	
@stop

@section('custom-js')

	<!-- DATA TABES SCRIPT -->
	<script src="{{asset('sms/plugins/datatables/jquery.dataTables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('sms/plugins/datatables/dataTables.bootstrap.min.js')}}" type="text/javascript"></script>
	<script type="text/javascript">
	  $(function () {
	    $("#pageList").dataTable();
	    $("#pageList2").dataTable();
	  });
	</script>
	<!-- Editor SCRIPT -->
	<script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
	<script src="{{Config::get('app.url')}}/vendor/text-editor/ckeditor/ckeditor.js"></script>
	<script>
			$(document).ready(function()
			{
				var roxyFileman = "{{Config::get('app.url')}}/vendor/text-editor/fileman?integration=ckeditor";
				$(function()
				{
					CKEDITOR.replace( "editor1",{filebrowserBrowseUrl:roxyFileman,
										filebrowserImageBrowseUrl:roxyFileman+"&type=image",
										removeDialogTabs: "link:upload;image:upload"});
				});
				
			});
	</script>;

  <script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue',
    increaseArea: '20%' // optional
  });
});
</script>

	
@stop