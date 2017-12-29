@extends('fee-manager.views.tabs')

@section('tab-content')
<form method="post" action="{{URL::route('fee-manager-hostel-fee-post')}}">
	<div class="row">                        
	  <div class="col-sm-3">
	    <div class="form-group ">
	      <label>Choose Class</label>
	      {{HelperController::generateSelectList('Classes', 'class_code', 'id', 'class_id', 
	        $selected = Input::old('class_id')?Input::old('class_id'):'', $condition = array(['field'=>'academic_session_id', 'operator'=>'=', 'value'=>HelperController::getCurrentSession()])
	          )}}
	    </div>
	  </div>                         
	  <div class="col-sm-3">
	    <div class="form-group">
	      <label>Select section</label>
	      <select name="section_id" id="section_id" class="form-control">
					<option value="0">--Select Class First--</option>
				</select>
	    </div>
	  </div>
	  <div class="col-sm-3">
	    <div class="form-group ">
	      <label>Type</label>
	      <select class="form-control" name='type'>
	        <option value='day' @if(Input::old('type')=='day') selected @endif >Day</option>
	        <option value='full' @if(Input::old('type')=='full') selected @endif>Full</option>
	      </select>
	    </div>
	  </div>
	  <div class="col-sm-3">
	    <div class="form-group @if($errors->has("amount")) {{"has-error"}} @endif">
	      <label for="catname">Amount</label>
	         <input id="catname" class="form-control" type="text" name="amount" value="{{Input::old('amount')?Input::old('amount'):''}}"/>
	         <span class = 'help-block'>@if($errors->has('amount')) {{$errors->first('amount')}} @endif</span>
	      </div>
	  </div>                    
	</div><!-- row ends -->
	<input type="hidden" name="is_active" value="yes" />
	{{Form::token()}}
	<button class="btn btn-success" type="submit"  @if(!AccessController::checkPermission('fee-manager', 'can_create')) disabled @endif>Create</button>
</form>
<br/><br/>

@if(count($data['data']))
<table id="pageList" class="table table-bordered table-striped">
  <thead>
  	<tr>
	    <th>SN</th>
	    <th>Class</th>
	    <th>Section</th>
	    <th>Type</th>
	    <th>Amount</th>
	    <th>Action</th>
    </tr>
  </thead>
  <tbody>
  	@define $i=1
  	@foreach($data['data'] as $d)
  	<tr>
	    <td>{{$i++}}</td>
	    <td>{{$d->class_code}}</td>
	    <td>{{$d->section_name}}</td>
	    <td>{{$d->type}}</td>
	    <td>{{$d->amount}}</td>
	    <td>
		    <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('fee-manager', 'can_delete')) disabled @endif>
	        <i class="fa fa-fw fa-trash"></i>
	      </a>
	  		@include($module_name.'.views.delete-hostel-fee-modal')
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