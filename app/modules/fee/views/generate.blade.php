@extends('backend.superadmin.main')

@section('content')
<form method="post" action="{{URL::route('fee-generate-post')}}">
	<div class="row">
	    <div class="col-sm-3">
	      <div class="form-group @if($errors->has('class_id')) has-error @endif">
	        <label>Select class</label>
	        {{HelperController::generateSelectList('Classes', 'class_code', 'id', 'class_id', 
								        $selected = Input::old('class_id')?Input::old('class_id'):(Input::has('class_id')?Input::get('class_id'):''), $condition = array(['field'=>'academic_session_id', 'operator'=>'=', 'value'=>HelperController::getCurrentSession()])
								          )}}
					<span class = 'help-block'>@if($errors->has('class_id')) {{$errors->first('class_id')}} @endif</span>
	      </div>
	    </div>
	    <div class="col-sm-3">
	      <div class="form-group">
	        <label>section</label>
	        <select name="section_id" id="section_id" class="form-control">
						<option value="0">--Select Class First--</option>
					</select>
	      </div>
	    </div>
	    <div class="col-sm-3">
	      <div class="form-group">
	        <label>Month</label>
	        @define $default_month=Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
						          
	        @define $months=array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December')


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
	<div class="row">
	  <div class="col-sm-3">
	    <button class="btn btn-block btn-success btn-lg" type="submit">
	      <i class="fa fa-fw fa-bar-chart"></i> Generate Report
	    </button>
	  </div>
	</div>   
</form>

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
  		var default_section = "{{Input::old('section_id')}}";

  		if(default_section=="") {
  			default_section = /section_id=([^&]+)/.exec(location.search);
  			default_section = default_section ? default_section[1] : '';
  		} 
      
  		updateSectionList(default_section, 'All');
  	}
  });

</script>
@stop