<!-- CAS-v1-changes-made-here -->
@extends('backend.'.$role.'.main')
<!-- CAS-v1-changes-made-here -->

@section('custom-css')
<link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
@stop


@section('page-header')
  <h1>CAS Setting</h1>

@stop

@section('content')
  <div class="row">
  <div class="col-sm-7">
 
    <form action = "{{URL::route('cas-setting-post')}}" method = "post">
      <div class="form-group">
          <label>
            Show percentage?
          </label>
          <label style="color:#085993">(describe here)</label><br/>
         @if($access['show_percentage'] == "yes")
          <label>
            <input type="radio" name="show_percentage" class="minimal"  value = "yes" checked/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="show_percentage" value = "no" class="minimal"/> No
          </label>
          @else
          <label>
            <input type="radio" name="show_percentage" class="minimal"  value = "yes"/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="show_percentage" value = "no" class="minimal" checked/> No
          </label>
          @endif
          </div>

          <div class="form-group">
          <label>
            Show grade?
          </label><label style="color:#085993">(describe here)</label><br/>
          @if($access['show_grade'] == "yes")
          <label>
            <input type="radio" name="show_grade" class="minimal"  value = "yes" checked/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="show_grade" value = "no" class="minimal"/> No
          </label>
          @else
          <label>
            <input type="radio" name="show_grade" class="minimal"  value = "yes"/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="show_grade" value = "no" class="minimal" checked/> No
          </label>
          @endif
          </div>

          <div class="form-group">
          <label>
          Show grade point?
          </label><label style="color:#085993">(describe here)</label><br/>
          @if($access['show_grade_point'] == "yes")
          <label>
            <input type="radio" name="show_grade_point" class="minimal"  value = "yes" checked/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="show_grade_point" value = "no" class="minimal"/> No
          </label>
          @else
          <label>
            <input type="radio" name="show_grade_point" class="minimal"  value = "yes"/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="show_grade_point" value = "no" class="minimal" checked/> No
          </label>
          @endif
          </div>

        <div class="form-group">
          <label for="fee_category">CAS Percentage</label><label style="color:#085993">(describe here)</label>
          <input id="fee_category" class="form-control" type="text" name = "cas_percentage" value="{{$access['cas_percentage']}}">
        </div>  
        <div class="form-group">
          <label for="fee_category">CAS Pass Percentage</label><label style="color:#085993">(describe here)</label>
          <input id="fee_category" class="form-control" type="text" name = "cas_pass_percentage" value="{{$access['cas_pass_percentage']}}">
        </div> 
        
        <button class="btn btn-flat btn-success btn-lg">Save </button>         
    {{Form::token()}}
   
    </form>
    </div>
    </div>
@stop


@section('custom-js')

@stop