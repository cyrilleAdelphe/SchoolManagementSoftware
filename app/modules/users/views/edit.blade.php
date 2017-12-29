@extends('users.views.tabs')

@section('tab-content')
             

	    <form method = "post" action = "{{URL::route($module_name.'-edit-post', array($data->id))}}" id = "backendForm" enctype = "multipart/form-data">
	        <div class = 'form-group'>
				<p>{{$data->username}}</p>
			</div>

			<div class = 'form-group'>
				<p>{{$data->name}}</p>
			</div>

			<div class = 'form-group'>
				<p>{{$data->role}}</p>
			</div>

			
			<div class = "form-group">
				<label for = "is_active">Is Active:</label>
				<input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') checked @endif>Yes<input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') checked @endif>No
			</div>
			{{Form::token()}}
			<input type = "hidden" name = "id" value = "{{$data->id}}">
			</div>
			<div class="form-group">
	            <button class="btn btn-primary" type="submit">Submit</button>
	        </div>                    
	    </form>
     
@stop

