@extends('fee-manager.views.tabs')

@section('tab-content')
<form method="post" action="{{URL::route('fee-manager-examination-fee-post')}}">
	<div class="row">                        
	  <div class="col-sm-3">
	    <div class="form-group @if($errors->has("class_id")) {{"has-error"}} @endif">
	      <label>Choose exam</label>
	      {{HelperController::generateSelectList('ExamConfiguration', 'exam_name', 'id', 'exam_id', 
	      $selected = Input::old('exam_id')?Input::old('exam_id'):'', $condition = array(['field'=>'session_id', 'operator'=>'=', 'value'=>HelperController::getCurrentSession()]), true
	        )}}
	        <span class = 'help-block'>@if($errors->has('exam_id')) {{$errors->first('exam_id')}} @endif</span>
	    </div>
	  </div> 
	  <div class="col-sm-3">
	    <div class="form-group @if($errors->has("amount")) {{"has-error"}} @endif">
	      <label for="catname">Enter Amount</label>
	         <input id="catname" name="amount" class="form-control" type="text" value="{{Input::old('amount')?Input::old('amount'):''}}"/>
		       <span class = 'help-block'>@if($errors->has('amount')) {{$errors->first('amount')}} @endif</span>
	      </div>
	  </div> 
	  <div class="col-sm-3">
	    <div class="form-group @if($errors->has("class_id")) {{"has-error"}} @endif">
	      <label>Choose class</label>
	      {{HelperController::generateSelectList('Classes', 'class_code', 'id', 'class_id', 
	      $selected = Input::old('class_id')?Input::old('class_id'):'', $condition = array(['field'=>'academic_session_id', 'operator'=>'=', 'value'=>HelperController::getCurrentSession()])
	        )}}
	        <span class = 'help-block'>@if($errors->has('class_id')) {{$errors->first('class_id')}} @endif</span>
	    </div>
	  </div>   
	  <div class="col-sm-2">
      <div class="form-group ">
        <label>Month</label>
        @if(CALENDAR=='BS')
          @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):HelperController::getCurrentNepaliMonth())
          @define $months = array('Baisakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra');
        @else
          @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
          @define $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December')
        @endif

        <select class="form-control" name="month">
        @foreach($months as $key=>$month_name)
        	@define $month_id = $key+1
        	<option value="{{$month_id}}" @if($default_month==$month_id) selected @endif>{{$month_name}}</option>
        @endforeach
        </select>
      </div>
    </div>                    
	</div><!-- row ends -->
	<input type="hidden" name="is_active" value="yes" />
	{{Form::token()}}
	<button class="btn btn-success" type="submit" @if(!AccessController::checkPermission('fee-manager', 'can_create')) disabled @endif>Create</button>
</form>
<br/><br/>

@if(count($data['data']))
<table id="pageList" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>SN</th>
      <th>Exam</th>
      <th>Amount</th>
      <th>Class</th>
      <th>Month</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  	@define $i=1
  	@foreach($data['data'] as $d)
    <tr>
      <td>{{$i++}}</td>
      <td>{{$d->exam_name}}</td>
      <td>{{$d->amount}}</td>
      <td>{{$d->class_code}}</td>
      <td>{{$months[$d->month-1]}}</td>
      <td>
        <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('fee-manager', 'can_delete')) disabled @endif>
	        <i class="fa fa-fw fa-trash"></i>
	      </a>
	  		@include($module_name.'.views.delete-examination-fee-modal')
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
<div class = "container">
  <div class = 'paginate'>
    @if($data['count'])
      {{$data['data']->appends($queryString)->links()}}
      
    @endif
  </div>
</div>
@endif

@stop