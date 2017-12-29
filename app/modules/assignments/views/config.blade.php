@extends('assignments.views.tabs')

@section('tab-content')
    
  <form method="post" action="{{URL::route('assignments-config-post')}}">

    <div class="form-group @if($errors->has("max_file_size")) {{"has-error"}} @endif">
      <label for="class" name="max_file_size">Max. file size(KB)</label>
      <input id="class" name="max_file_size" class="form-control" type="text" value="{{$assignment_config->max_file_size}}">
      <span class = "form-error">
        @if($errors->has('max_file_size')) 
          {{ $errors->first('max_file_size') }} 
        @endif
      </span>
    </div>

    <div class="form-group @if($errors->has("max_frontend_show")) {{"has-error"}} @endif">
      <label for="class" name="max_frontend_show">Max. frontend Show</label>
      <input id="class" name="max_frontend_show" class="form-control" type="text" value="{{$assignment_config->max_frontend_show}}">
      <span class = "form-error">
        @if($errors->has('max_frontend_show')) 
          {{ $errors->first('max_frontend_show') }} 
        @endif
      </span>
    </div>
    {{Form::token()}}
    <div class="form-group">
      <button class="btn btn-primary" type="submit">Submit</button>
    </div>
  </form>
@stop

@section('custom-js')

<script src="{{ asset('backend-js/validation.js') }}" type="text/javascript"></script>

@stop
              