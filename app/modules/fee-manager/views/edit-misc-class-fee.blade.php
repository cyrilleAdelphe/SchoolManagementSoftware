@extends('backend.'.$role.'.main')

@section('content')
<form method="post" action="{{URL::route('fee-manager-misc-class-fee-edit-post', $data->id)}}">
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
             <input id="catname" class="form-control" type="text" name= "amount" value="{{Input::old('amount')?Input::old('amount'):$data->amount}}"/>
          <span class = 'help-block'>@if($errors->has('amount')) {{$errors->first('amount')}} @endif</span>
          </div>
      </div> 
      <div class="col-sm-3">
        <div class="form-group @if($errors->has("class_id")) {{"has-error"}} @endif">
		      <label>Choose Class</label>
		      {{HelperController::generateSelectList('Classes', 'class_code', 'id', 'class_id', 
		        $selected = Input::old('class_id')?Input::old('class_id'):$data->class_id, $condition = array(['field'=>'academic_session_id', 'operator'=>'=', 'value'=>HelperController::getCurrentSession()])
		          )}}
		      <span class = 'help-block'>@if($errors->has('class_id')) {{$errors->first('class_id')}} @endif</span>
		    </div>
      </div>
      <div class="col-sm-2">
        <div class="form-group @if($errors->has("section_id")) {{"has-error"}} @endif">
		      <label>Select section</label>
		      <select name="section_id" id="section_id" class="form-control">
						<option value="0">--Select Class First--</option>
					</select>
					<span class = 'help-block'>@if($errors->has('section_id')) {{$errors->first('section_id')}} @endif</span>
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
    </div><!-- row ends -->
    <input type="hidden" name="id" value="{{$data->id}}" />
    <input type="hidden" name="is_active" value="yes" />
    {{Form::token()}}
    <button class="btn btn-success" type="submit">Submit</button>
  </div>
</form>

<input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />
@stop

@section('custom-js')

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script>
	
	$(document).on('change', '#class_id', updateSectionList);

  $(function() {
  	if($('#class_id').val() !=0) {
  		updateSectionList("{{Input::old('section_id')?Input::old('section_id'):$data->section_id}}");
  	}
  });

</script>

@stop