@extends('backend.'.$role.'.main')

@section('content')
<form method="post" action="{{URL::route('fee-manager-misc-student-fee-edit-post', $data->id)}}">
  <div class="box-body">
    <div class="row">                      
      <div class="col-sm-3">
        <div class="form-group @if($errors->has("title")) {{"has-error"}} @endif">
          <label for="catname">Title</label>
          <input id="catname" class="form-control" type="text" name= "title" value="{{Input::old('title')?Input::old('title'):$data->title}}"/>
          <span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
        </div>
      </div> 
      <div class="col-sm-2">
        <div class="form-group @if($errors->has("amount")) {{"has-error"}} @endif">
          <label for="catname">Enter Amount</label>
          <input id="catname" class="form-control" type="text" name="amount" value="{{Input::old('amount')?Input::old('amount'):$data->amount}}" />
            <span class = 'help-block'>@if($errors->has('amount')) {{$errors->first('amount')}} @endif</span>
          </div>
      </div> 
      <div class="col-sm-3">
        <div class="form-group @if($errors->has("student_id")) {{"has-error"}} @endif">
          <label for="catname">Student's ID</label>
          <input id="catname" class="form-control" type="text" name="student_id" value="{{Input::old('student_id')?Input::old('student_id'):$data->student_id}}" />
          <span class = 'help-block'>@if($errors->has('student_id')) {{$errors->first('student_id')}} @endif</span>
        </div>
      </div> 
      <div class="col-sm-2">
        <div class="form-group ">
          <label>Month</label>
          @define $default_month = Input::old('month', $data->month)
          @if (CALENDAR=='BS')
            @define $months = array_merge(['Recurring'], HelperController::getNepaliMonths());
          @else
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
          @foreach($months as $key=>$month)
          	@define $month_id = $key
          	<option value="{{$month_id}}" @if($default_month==$month_id) selected @endif>{{$month}}</option>
          @endforeach
          </select>
        </div>
      </div>  
      <div class="col-sm-3">
		    <div class="form-group">
		      <label>Select Session</label>
		      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
		        $selected = 
		          Input::has('academic_session_id') ?
		          Input::get('academic_session_id') : $data->academic_session_id)}}
		    </div>
		  </div>                   
    </div><!-- row ends -->
		<input type="hidden" name="id" value="{{$data->id}}" />
    <input type="hidden" name="is_active" value="yes" />
    {{Form::token()}}
    <button class="btn btn-success" type="submit">Submit</button>
  </div>
</form>
@stop