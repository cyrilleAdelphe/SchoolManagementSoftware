@extends('fee-manager.views.tabs')

@section('tab-content')

<form method="post" action="{{URL::route('fee-manager-misc-class-fee-post')}}">
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
             <input id="catname" class="form-control" type="text" name= "amount" value="{{Input::old('amount')?Input::old('amount'):''}}"/>
          <span class = 'help-block'>@if($errors->has('amount')) {{$errors->first('amount')}} @endif</span>
          </div>
      </div> 
      <div class="col-sm-3">
        <div class="form-group ">
		      <label>Choose Class</label>
		      {{HelperController::generateSelectList('Classes', 'class_code', 'id', 'class_id', 
		        $selected = Input::old('class_id')?Input::old('class_id'):'', $condition = array(['field'=>'academic_session_id', 'operator'=>'=', 'value'=>HelperController::getCurrentSession()])
		          )}}
		    </div>
      </div>
      <div class="col-sm-2">
        <div class="form-group">
		      <label>Select section</label>
		      <select name="section_id" id="section_id" class="form-control">
						<option value="0">--Select Class First--</option>
					</select>
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
          @foreach ($months as $key => $month_name)
          	@define $month_id = $key
          	<option value="{{$month_id}}" @if($default_month==$month_id) selected @endif>{{$month_name}}</option>
          @endforeach
          </select>
        </div>
      </div>                     
    </div><!-- row ends -->
    <input type="hidden" name="is_active" value="yes" />
    {{Form::token()}}
    <button class="btn btn-success" type="submit"  @if(!AccessController::checkPermission('fee-manager', 'can_create')) disabled @endif>Create</button>
  </div>
</form>
    

@if(count($data['class']))
<h4 class="text-red">Class record</h4>   
<table id="pageList" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>SN</th>
      <th>Title</th>
      <th>Amount</th>
      <th>Session</th>
      <th>Class</th>
      <th>Section</th>
      <th>Month</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  	@define $i=1
  	@foreach($data['class'] as $d)
    <tr>
      <td>{{ $i++ }}</td>
      <td>{{ $d->title }}</td>
      <td>{{ $d->amount }}</td>
      <td>{{ $d->session_name }}</td>
      <td>{{ $d->class_code }}</td>
      <td>{{ $d->section_code }}</td>
      <td>{{ $months[$d->month] }}</td>
      <td>
        <a href="{{URL::route('fee-manager-misc-class-fee-edit-get', $d->id)}}" data-toggle="tooltip" title="Edit" class="btn btn-info btn-flat" type="button"  @if(!AccessController::checkPermission('fee-manager', 'can_edit')) disabled @endif>
          <i class="fa fa-fw fa-edit"></i>
        </a>
        <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('fee-manager', 'can_delete')) disabled @endif>
	        <i class="fa fa-fw fa-trash"></i>
	      </a>
	  		@include($module_name.'.views.delete-misc-class-fee-modal')
      </td>
    </tr>
    @endforeach
    
  </tbody>
</table>
<div class = "container">
  <div class = 'paginate'>
    @if($data['class'])
      {{$data['class']->appends($queryString)->links()}}
      
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

</script>

@stop