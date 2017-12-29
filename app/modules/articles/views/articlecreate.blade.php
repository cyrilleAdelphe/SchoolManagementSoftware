<form method = "post" action = "{{URL::route('articles-create-post')}}">
  <div class="form-group">
      <label for="title">Title</label> 
      <input name = "title" id="title" class="form-control" type="text" placeholder="Enter article title" value= "{{ (Input::old('title')) ? (Input::old('title')) : '' }}">
      <span class = "form-error">@if($errors->has('title')) {{ $errors->first('title') }} @endif</span>
  </div>
  
  <div class="form-group">
      <label for="title">Alise</label>
      <input name = "alias" id="alise" class="form-control" type="text" placeholder="Enter unique alise" value= "{{ (Input::old('alias')) ? (Input::old('alias')) : '' }}">
      <span class = "form-error">@if($errors->has('alias')) {{ $errors->first('alias') }} @endif</span>
  </div>

  <div class="form-group">
    <label for="title">Meta Tag</label>
    <input name = "meta_tag" id="meta_tag" class="form-control" type="text" placeholder="Enter meta tag" value= "{{ (Input::old('meta_tag')) ? (Input::old('meta_tag')) : '' }}">
    <span class = "form-error">@if($errors->has('meta_tag')) {{ $errors->first('meta_tag') }} @endif</span>
  </div>

  <div class="form-group">
    <label for="content">Meta Description</label>
    <textarea name = "meta_description" class="metaDescription" placeholder="Place some text here" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" >{{ (Input::old('meta_description')) ? (Input::old('meta_description')) : '' }}</textarea>
    <span class = "form-error">@if($errors->has('meta_description')) {{ $errors->first('meta_description') }} @endif</span>
  </div>

  <div class="form-group">
  	<label for="category"> Category </label>
  	<select name="category_id" class="form-control">
  		@foreach($categories as $category)
  			<option value= "{{ $category['id'] }}"> {{ $category['title'] }} </option>
  		@endforeach
  	</select>
  </div>

  <div class = "form-group">
      <label for = "is_featured">Is Featured:</label>&nbsp;&nbsp;&nbsp;
      <input type = "radio" name = "is_featured" value = "yes" @if(Input::old('is_featured') == 'yes') checked @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type = "radio" name = "is_featured" value = "no" @if(Input::old('is_featured') == 'no') checked @endif>&nbsp;&nbsp;No
  </div>

  <div class="form-group">
      <label for="content">Article content</label>
      <textarea id='editor1' name = "content" class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"> {{ (Input::old('content')) ? (Input::old('content')) : '' }} </textarea>
      <span class = "form-error">@if($errors->has('content')) {{ $errors->first('content') }} @endif</span>
  </div>
  <div class="form-group">
      <button class="btn btn-primary btn-lg btn-flat" type="submit">Submit</button>
  </div>
  {{Form::token()}}
</form>