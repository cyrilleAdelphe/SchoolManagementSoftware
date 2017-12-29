@extends('backend.' . $role . '.main')

@section('content')
  <form method="post" action="{{URL::route('settings-general-post')}}" enctype = "multipart/form-data">
    
    @foreach($general_settings as $name => $value)
    <div class="form-group @if($errors->has($name)) {{"has-error"}} @endif">
      <label for="class" name="{{$name}}">{{HelperController::underscoreToSpace($name)}}</label>
      <input id="class" name="{{$name}}" class="form-control" type="text" value="{{$value}}" required>
      <span class = "form-error">
        @if($errors->has($name)) 
          {{ $errors->first($name) }} 
        @endif
      </span>
    </div>
    @endforeach

    <div class="form-group @if($errors->has("school_logo")) {{"has-error"}} @endif">
      <label for="class" name="{{$name}}">School Logo:</label>
      <input type = "file" name = "school_logo">
      <span class = "form-error">
        @if($errors->has("school_logo")) 
          {{ $errors->first("school_logo") }} 
        @endif
      </span>
      <div style=" max-width: 250px;">
        @if (File::exists(SCHOOL_LOGO_LOCATION . '/' . SCHOOL_LOGO_FILENAME))
          <img class="img-responsive" src = "{{SCHOOL_LOGO_URL}}" />
        @else
          <img class="img-responsive" src = "{{Config::get('app.url').'app/modules/student/assets/images/no-img.png'}}" />
        @endif
        
      </div>

    {{Form::token()}}
    <div class="form-group">
      <button class="btn btn-success btn-lg btn-flat" type="submit">Submit</button>
    </div>
  </form>
@stop
            