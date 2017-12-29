@extends('fee-manager.views.tabs')

@section('tab-content')
<form method="post" action="{{URL::route('fee-manager-scholarship-post')}}">
  <div class="box-body">
  	<div class="row">                      
  		<div class="col-sm-2">
        <div class="form-group ">
          <label>Type</label>
          @define $scholarship_types = array('monthly', 'hostel', 'transportation')
          <select class="form-control" name="type">
          @foreach($scholarship_types as $type)
          	<option value="{{$type}}">{{ucfirst($type)}}</option>
          @endforeach
          </select>
        </div>
      </div>

      <div class="col-sm-2">
        <div class="form-group @if($errors->has("percent")) {{"has-error"}} @endif">
          <label for="catname">Enter Percent</label>
          <input id="catname" class="form-control" type="text" name="percent" value="{{Input::old('percent')?Input::old('percent'):''}}" />
          <span class = 'help-block'>@if($errors->has('percent')) {{$errors->first('percent')}} @endif</span>
        </div>
      </div> 

      <div class="col-sm-3">
        <div class="form-group @if($errors->has("student_id")) {{"has-error"}} @endif">
          <label for="catname">Student's Username</label>
          <input id="studentUsername" class="form-control" type="text" name="student_id" value="{{Input::old('student_id')?Input::old('student_id'):''}}" />
          <span class = 'help-block'>@if($errors->has('student_id')) {{$errors->first('student_id')}} @endif</span>
          <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
          @include('include.modal-find-student')
        </div>
      </div> 

    </div><!-- row ends -->

    <input type="hidden" name="academic_session_id" value="{{HelperController::getCurrentSession()}}" />
    <input type="hidden" name="is_active" value="yes" />
    {{Form::token()}}
    <button class="btn btn-success" type="submit"  @if(!AccessController::checkPermission('fee-manager', 'can_create')) disabled @endif>Create</button>
  </div>
</form>

@if(count($data['student']))
<h4 class="text-red">Individual students record</h4>   
<table id="pageList" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>SN</th>
      <th>Type</th>
      <th>Percent</th>
      <th>Session</th>
      <th>Student Name</th>
      <th>Student ID</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  	@define $i=1
  	@foreach($data['student'] as $d)
    <tr>
      <td>{{$i++}}</td>
      <td>{{ucfirst($d->type)}}</td>
      <td>{{$d->percent}}</td>
      <td>{{$d->session_name}}</td>
      <td>
      	<a href="#" data-toggle="modal" data-target="#student{{$d->student_id}}" data-toggle="tooltip" title="Student Info">
      		{{$d->student_name}}
      	</a>
      	@include($module_name.'.views.student-info-modal')
      </td>
      <td>{{$d->student_id}}</td>
      <td>
        <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('fee-manager', 'can_delete')) disabled @endif>
	        <i class="fa fa-fw fa-trash"></i>
	      </a>
	  		@include($module_name.'.views.delete-scholarship-model')
      </td>
    </tr>
    @endforeach
    
  </tbody>
</table>
<div class = "container">
  <div class = 'paginate'>
    @if(count($data['student']))
      {{$data['student']->appends($queryString)->links()}}
      
    @endif
  </div>
</div>
@endif

@stop

@section('custom-js')
<script>
// event trigger for select button after searching student
  function findIdSelect(username) {
    $('#studentUsername').val(username);
  }
</script>
@stop