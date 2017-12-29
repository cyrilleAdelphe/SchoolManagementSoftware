@extends('gallery.views.tabs')

@section('tab-content')
	<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-create-post')}}" enctype = "multipart/form-data">
			<div class = 'form-group @if($errors->has("title")) {{"has-error"}} @endif'>
				<label for = 'title'  class = 'control-label'>Title :</label>
					
				<input type = 'text' name = 'title' value= '{{ (Input::old('title')) ? (Input::old('title')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("description")) {{"has-error"}} @endif'>
				<label for = 'description'  class = 'control-label'>Description :</label>
					
				<input type = 'text' name = 'description' value= '{{ (Input::old('description')) ? (Input::old('description')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('description')) {{$errors->first('description')}} @endif</span>
					
			</div>

			<div class="form-group @if($errors->has("category_id")) {{"has-error"}} @endif">
	      <label>Category</label>
	      {{HelperController::generateSelectList('GalleryCategory', 'title', 'id', 'category_id', 
	        $selected = 
	          Input::has('category_id') ?
	          Input::get('category_id') : '')}}
	    </div>

			<div class = 'form-group @if($errors->has("images")) {{"has-error"}} @endif'>
				<label for = 'images'  class = 'control-label'>Images :</label>
				<input type = 'file' name = 'images[]' class = ' required' multiple>
				<span class = 'help-block'>@if($errors->has('images')) {{$errors->first('images')}} @endif</span>	
			</div>

			<input type="hidden" name="is_active" value="yes">

			{{Form::token()}}
			
			<div class = "form-group">
				<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Create">
			</div>

		</form>
	</div>
@stop
