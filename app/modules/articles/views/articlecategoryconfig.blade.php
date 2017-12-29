


	<form method = "post" action = "{{URL::route('articles-category-config-post')}}">
		<div class="form-row">
			<label for="max_articles">No. of articles</label> 
			<input name = "max_articles" id="max_articles" class="form-control" type="text" placeholder="Enter number of articles" 
				value= "{{ (Input::old('max_articles')) ? (Input::old('max_articles')) :
							 ( isset($category_config) ? $category_config['max_articles'] : '') }}">
			<span class = "form-error">
				@if($errors->has('max_articles')) 
					{{ $errors->first('max_articles') }} 
				@endif
			</span>
		</div>

		<div class="form-row">
			<label for="max_words">Maximum number of words</label> 
			<input name = "max_words" id="max_words" class="form-control" type="text" placeholder="Enter maximum number of words" 
				value= "{{ (Input::old('max_words')) ? (Input::old('max_words')) : 
							(isset($category_config) ? $category_config['max_words'] : '') }}">
			<span class = "form-error">
				@if($errors->has('max_words'))
					{{ $errors->first('max_words') }} 
				@endif
			</span>
		</div>
		
		<div class="form-group">
			<br/>
			<button class="btn btn-primary btn-flat btn-lg" type="submit">Submit</button>
		</div>
		{{ Form::token() }}
	</form>
