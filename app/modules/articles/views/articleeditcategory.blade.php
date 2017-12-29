@extends('backend.superadmin.main')

@section('custom-css')
	<!-- Theme style -->    
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')    
  <h1>Edit Category</h1>
@stop

@section('content')
	    <!-- Content Header (Page header) -->
<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
	          	 <form method = "post" action = "{{URL::route('articles-edit-category-post')}}">
	          	 	<?php
	          	 		/*
							| This segment of code select value to be placed in the form field
							| When the edit button is pressed from the articles' main page, you would want the values in database.
							| When the user has edited the article but the input isn't validated, you would want the edited values
	          	 		*/
	          	 		$value = [];
	          	 		$fields = ['title'];
	          	 		foreach($fields as $field)
	          	 		{
	          	 			$value[$field] = '';
		          	 		if (Input::old($field))
		          	 		{
		          	 			$value[$field] = Input::old($field);
		          	 		}
							else
							{
		          	 			 $value[$field] = $category[$field];
							}
						}
	          	 	?>
                    <div class="form-group">
                    	<label for="title">Title</label>
                        <input name = "title" id="title" class="form-control" type="text" value = "{{ $value['title'] }}" >
                        <span class = "form-error">@if($errors->has('title')) {{ $errors->first('title') }} @endif</span>
                    </div>
                    
                    <div class="form-row">
						<label for="frontend_publishable">Frontend publish</label> 
						<input type="hidden" name="frontend_publishable" value="0" />
						<input name = "frontend_publishable" id="frontend_publishable" type="checkbox" value="1" {{ $category['frontend_publishable']=="1" ?'checked = "checked"' : ''}}>
					</div>

					<input type="hidden" name="id" value="{{$category['id']}}"/>
                    {{Form::token()}}
                    
                    <div class="form-group">
                        <button class="btn btn-success btn-lg btn-flat" type="submit">Update</button>
                    </div>
                    
                </form>
	
@stop

@section('custom-js')

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