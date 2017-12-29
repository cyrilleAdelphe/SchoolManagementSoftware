@extends('backend.superadmin.submain')
@section('content')
<div class="content">
  <div class="row">
    <div class="col-sm-6">
      <form method="POST" action="{{ URL::route('update-house', $house->id) }}" target="_top">
        <input type="hidden" name="active_tab" value="tab_2">
        <div class="form-group">
          <label>House name *</label>
          <input class="form-control" type="text" name="house_name" value="{{ $house->house_name}}" required>
        </div>
        <div style="color:red;">{{ $errors->first('house_name')}}</div>
                            
        <div class="form-group">
          <label>House Code *</label>
          <input class="form-control" type="text" name="house_code" value="{{ $house->house_code}}" required>
        </div>
        <div style="color:red;">{{ $errors->first('house_code')}}</div>
                            
        <div class="form-group" >
          Is Active:
          <input type="radio" name="is_active" value="yes" @if($house->is_active == 'yes') checked @endif>Yes
          <input type="radio" name="is_active" value="no" @if($house->is_active == 'no') checked @endif>no
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
@stop