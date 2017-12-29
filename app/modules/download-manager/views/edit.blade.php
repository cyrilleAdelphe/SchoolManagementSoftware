@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
	<link href="{{ asset('sms/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
	<h1>
		Edit {{ $is_folder ? 'Category' : 'File' }}
	</h1>
@stop

@section('content')
	<section class="content">
		<div class="box">
          	<div class="box-body">
				<form method="post" action="{{ URL::route('download-manager-backend-edit-post') }}">
					<div class="form-group">
					  <label for="name">Name</label>
					  <input id="name" name="filename" class="form-control" type="text" placeholder="Enter new name"
					    value= "{{ (Input::old('filename')) ? (Input::old('filename')) : $name }}">
					    <span class = "form-error">
					      @if($errors->has('filename')) 
					        {{ $errors->first('filename') }} 
					      @endif
					    </span>
					</div>

					@if(!$is_folder)
						<div class="form-group">
						  <label for="tags">Tags</label>
						  <input id="tags" name="tags" class="form-control" type="text"
						    value= "{{ (Input::old('tags')) ? (Input::old('tags')) : $tags }}">
						    <span class = "form-error">
						      @if($errors->has('tags')) 
						        {{ $errors->first('tags') }} 
						      @endif
						    </span>
						</div>
					@endif

					<div class="form-group">
						<label for="content">Description</label>
					    <textarea class="textarea" name="description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
					        {{ (Input::old('description')) ? (Input::old('description')) : $description }}
					    </textarea>

					    <span class = "form-error">
					      @if($errors->has('description')) 
					        {{ $errors->first('description') }} 
					      @endif
					    </span>
					</div>
					
					<div class = "form-group">
						<label for = "parent_id">Parent:</label>
					  	<select name="parent_id" class="form-control">
					  		@foreach($categories as $category)
					  			<option value = "{{$category['id']}}" @if($category['id'] == $parent['id']) selected @endif>
					  				{{$category['filename']}}
					  			</option>
					  			
					  		@endforeach
					  	</select>
					</div>

					<div class = "form-group">
						<label for = "is_featured">Is Featured:</label>
						<input type = "radio" name = "is_featured" value = "yes" @if($file['is_featured'] == 'yes') checked @endif>Yes<input type = "radio" name = "is_featured" value = "no" @if($file['is_featured'] == 'no') checked @endif>No
					</div>

					<div class = "form-group">
						<label for = "is_active">Is Publishable:</label>
						<input type = "radio" name = "is_active" value = "yes" @if($file['is_active'] == 'yes') checked @endif>Yes<input type = "radio" name = "is_active" value = "no" @if($file['is_active'] == 'no') checked @endif>No
					</div>


					<div class="form-group">
					    <button class="btn btn-primary" type="submit">Submit</button>
					</div>

					<input type = "hidden" name = "mime_type" value = "{{ $mime_type }}"/>
					<input type = "hidden" name = "id" value = "{{ $id }}"/>
					<input type = "hidden" name = "google_file_id" value = "{{ $google_file_id }}"/>
					{{ Form::token() }}
				</form>
			</div>
		</div>
	</section>
@stop

@section('custom-js')
	<!-- jQuery 2.1.4 -->
    <script src="{{ asset('sms/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset('sms/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>    
    <!-- FastClick -->
    <script src="{{ asset('sms/plugins/fastclick/fastclick.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('sms/assets/js/app.min.js') }}" type="text/javascript"></script>
      
    <!-- Editor SCRIPT -->
    <script src="{{ asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
      $(function () {
        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();
      });
    </script>
@stop