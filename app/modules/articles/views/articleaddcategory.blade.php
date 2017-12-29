<form method = "post" action = "{{URL::route('articles-add-category-post')}}">
	<div class="form-group">
		<label for="title">Title</label> 
		<input name = "title" id="title" class="form-control" type="text" placeholder="Enter title of the category" 
			value= "{{ (Input::old('title')) ? (Input::old('title')) : '' }}">
		<span class = "form-error">
			@if($errors->has('title')) 
				{{ $errors->first('title') }} 
			@endif
		</span>
	</div>

	<div class="form-group">
		<label for="frontend_publishable">Frontend publish</label> 
		<input type="hidden" name="frontend_publishable" value="0" />
		<input name = "frontend_publishable" id="frontend_publishable" type="checkbox" value="1">
	</div>
	
	<div class="form-group">
		<button class="btn btn-primary btn-flat btn-lg" type="submit">Create Category</button>
	</div>
	{{ Form::token() }}
</form>