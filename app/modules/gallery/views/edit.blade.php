@extends('backend.'. $current_user->role . '.main')

@section('content')
	<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', array($id))}}" enctype = "multipart/form-data">
			<div class = 'form-group @if($errors->has("title")) {{"has-error"}} @endif'>
				<label for = 'title'  class = 'control-label'>Title :</label>
					
				<input type = 'text' name = 'title' value= '{{ (Input::old('title')) ? (Input::old('title')) : $data->title }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("description")) {{"has-error"}} @endif'>
				<label for = 'description'  class = 'control-label'>Description :</label>
					
				<input type = 'text' name = 'description' value= '{{ (Input::old('description')) ? (Input::old('description')) : $data->description }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('description')) {{$errors->first('description')}} @endif</span>
					
			</div>

			<div class="form-group @if($errors->has("category_id")) {{"has-error"}} @endif">
	      <label>Category</label>
	      {{HelperController::generateSelectList('GalleryCategory', 'title', 'id', 'category_id', 
	        $selected = 
	          Input::has('category_id') ?
	          Input::get('category_id') : $data->category_id)}}
	    </div>

			<div class = 'form-group @if($errors->has("image")) {{"has-error"}} @endif'>
				<label for = 'image'  class = 'control-label'>Image :</label>
				<input type = 'file' name = 'image' value= '{{ (Input::old('image')) ? (Input::old('image')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('image')) {{$errors->first('image')}} @endif</span>	
				<img class="dynamicImage" src="{{Config::get('app.url').'/app/modules/gallery/assets/images/original/'.$data->id}}" alt="{{$data->title}}">
			</div>

			<div class = 'form-group @if($errors->has("is_active")) {{"has-error"}} @endif'>
				<label for = 'is_active'  class = 'control-label'>Is Active:</label>
				<input type = 'radio' name = 'is_active' value= 'yes' @if($data->is_active == 'yes') checked @endif>Yes<input type = 'radio' name = 'is_active' value= 'no' @if($data->is_active == 'no') checked @endif>No
				<span class = 'help-block'>@if($errors->has('is_active')) {{$errors->first('is_active')}} @endif</span>
			</div>

			
			<input type = "hidden" name = "id" value = "{{$data->id}}">

			{{Form::token()}}
			
			<div class = "form-group">
				<input type = "submit" class = "btn btn-info" value = "Create">
			</div>

		</form>
	</div>
@stop

@section('custom-js')
<script src="{{Config::get('app.url').'/app/modules/gallery/assets/js/dynamicImages.js'}}"></script>
@stop
