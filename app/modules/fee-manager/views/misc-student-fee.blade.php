@extends('fee-manager.views.tabs')

@section('tab-content')

<form method="post" action="{{URL::route('fee-manager-misc-student-fee-post')}}">
  <div class="box-body">
    <div class="row">                      
      <div class="col-sm-3">
        <div class="form-group @if($errors->has("title")) {{"has-error"}} @endif">
          <label for="catname">Title</label>
          <input id="catname" class="form-control" type="text" name= "title" value="{{Input::old('title')?Input::old('title'):''}}"/>
          <span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
        </div>
      </div> 
      <div class="col-sm-2">
        <div class="form-group @if($errors->has("amount")) {{"has-error"}} @endif">
          <label for="catname">Enter Amount</label>
          <input id="catname" class="form-control" type="text" name="amount" value="{{Input::old('amount')?Input::old('amount'):''}}" />
            <span class = 'help-block'>@if($errors->has('amount')) {{$errors->first('amount')}} @endif</span>
          </div>
      </div> 
      <div class="col-sm-3">
        <div class="form-group @if($errors->has("student_id")) {{"has-error"}} @endif">
          <label for="catname">Student's username</label>
          <input id="studentUsername" class="form-control" type="text" name="student_id" value="{{Input::old('student_id')?Input::old('student_id'):''}}" />
          <span class = 'help-block'>@if($errors->has('student_id')) {{$errors->first('student_id')}} @endif</span>
          <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
          @include('include.modal-find-student')
        </div>
      </div> 
      <div class="col-sm-2">
        <div class="form-group ">
          <label>Month</label>
          
          @if (CALENDAR=='BS')
            @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):HelperController::getCurrentNepaliMonth())
            @define $months = array_merge(['Recurring'], HelperController::getNepaliMonths());
          @else
            @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
            <?php 
            $months = array_merge(['Recurring'], array_map(
              function($i) {
                return DateTime::createFromFormat('m', $i)->format('F');
              }, 
              range(1, 12)
            ));
            ?>
          @endif

          <select class="form-control" name="month">
          @foreach ($months as $key=>$month)
          	@define $month_id = $key
          	<option value="{{$month_id}}" @if($default_month==$month_id) selected @endif>{{$month}}</option>
          @endforeach
          </select>
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
      <th>Title</th>
      <th>Amount</th>
      <th>Session</th>
      <th>Student Name</th>
      <th>Student ID</th>
      <th>Month</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  	@define $i=1
  	@foreach($data['student'] as $d)
    <tr>
      <td>{{$i++}}</td>
      <td>{{$d->title}}</td>
      <td>{{$d->amount}}</td>
      <td>{{$d->session_name}}</td>
      <td>
      	<a href="#" data-toggle="modal" data-target="#student{{$d->student_id}}" data-toggle="tooltip" title="Student Info">
      		{{$d->student_name}}
      	</a>
      	@include($module_name.'.views.student-info-modal')
      </td>
      <td>{{$d->student_id}}</td>
      <td>{{$months[$d->month]}}</td>
      <td>
        <a href="{{URL::route('fee-manager-misc-student-fee-edit-get', $d->id)}}" data-toggle="tooltip" title="Edit" class="btn btn-info btn-flat" type="button"  @if(!AccessController::checkPermission('fee-manager', 'can_delete')) disabled @endif>
          <i class="fa fa-fw fa-edit"></i>
        </a>
        <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('fee-manager', 'can_delete')) disabled @endif>
	        <i class="fa fa-fw fa-trash"></i>
	      </a>
	  		@include($module_name.'.views.delete-misc-student-fee-modal')
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

<input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />
                   
@stop

@section('custom-js')

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script>
	
	$(document).on('change', '#class_id', function() {
    updateSectionList('', 'All');
  });

  $(function() {
  	if($('#class_id').val() !=0) {
  		updateSectionList('', 'All');
  	}
  });

  // event trigger for select button after searching student
  function findIdSelect(username) {
    $('#studentUsername').val(username);
  }

  

</script>

@stop