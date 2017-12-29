@extends('backend.superadmin.submain')
@section('content')
<div class="content">
  <div class="row">
    <div class="col-sm-6">
      <form method="POST" action="{{ URL::route('ethnicity-update', $ethnicity->id)}}" target="_top">
        <input type="hidden" name="active_tab" value="tab_2">
          <div class="form-group">
            <label>Ethnicity name *</label>
            <input class="form-control" type="text" name="ethnicity_name" value="{{ $ethnicity->ethnicity_name}}" required>
          </div>
          <div style="color:red;">{{ $errors->first('ethnicity_name')}}</div>
                          
          <div class="form-group">
            <label>Ethnicity Code *</label>
            <input class="form-control" type="text" name="ethnicity_code" value="{{ $ethnicity->ethnicity_code}}"  required>
          </div>
          <div style="color:red;">{{ $errors->first('ethnicity_code')}}</div>
                          
          <div class="form-group">
            Is Active:
            <input type="radio" name="is_active" value="yes" @if($ethnicity->is_active == 'yes')checked @endif >Yes
            <input type="radio" name="is_active" value="no" @if($ethnicity->is_active == 'no')checked @endif>no
            <div style="color:red;">{{ $errors->first('is_active')}}</div>
          </div>
          <div class="form-group">
            <button class="btn btn-success btn-flat btn-lg">Submit</button>
          </div>
          {{ Form::token() }}
      </form>
    </div>
  </div>
</div>
</div>                      
@stop