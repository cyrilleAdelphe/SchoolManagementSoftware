@extends('exam-configuration.views.tabs')

@section('tab-content')

	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">
		<div class="row">
		<div class="col-sm-12">
		<div class = 'form-group @if($errors->has("subject_name")) {{"has-error"}} @endif'>
			<label for = 'exam_name'  class = 'control-label'>Exam Name</label>
				
			<input id="exam_name" name="exam_name" class="form-control required" type="text" placeholder="Enter exam name"
                value= "{{ (Input::old('exam_name')) ? (Input::old('exam_name')) : '' }}">
            <span class = 'help-block'>
              @if($errors->has('exam_name')) {{$errors->first('exam_name')}} @endif
            </span>
		</div>

		<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
			<label for = 'session_id'  class = 'control-label'>Session</label>
				
			@define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();
              <select class = "form-control" id = "session_id" name = "session_id">
                @foreach($sessions as $s)
                @if($s->is_current == 'yes')
                  @define $current_session = $s->id
                @endif
                <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
                @endforeach
              </select>
		</div>

		<div class = 'form-group @if($errors->has("parent_exam_id")) {{"has-error"}} @endif'>
			<label for = 'parent_exam_id'  class = 'control-label'>Parent Exam</label>
				
			<select id = 'parent_exam_id' class = "form-control" name = "parent_exam_id">
			@define $exams = ExamConfiguration::where('session_id', $current_session)->lists('exam_name', 'id');

			<option value = "0">Root</option>
			@foreach($exams as $exam_id => $exam_name)
			<option value = "{{$exam_id}}">{{$exam_name}}</option>
			@endforeach
			</select>
		</div>

		<div class="form-group @if($errors->has("exam_start_date_in_ad")) {{"has-error"}} @endif">
      <label>Start date:</label>

      <input type="text" class="form-control myDate required" name = "exam_start_date_in_ad" data-mask="" data-inputmask="'alias': 'yyyy-mm-dd'" class="form-control" placeholder="Enter Joining date" value = "{{Input::old('exam_start_date_in_ad')}}">
      <span class = 'help-block'>@if($errors->has('exam_start_date_in_ad')) {{$errors->first('exam_start_date_in_ad')}} @endif</span>
		    
		</div>

		<div class="form-group @if($errors->has("exam_end_date_in_ad")) {{"has-error"}} @endif">
      <label>End date:</label>
      
      <input type="text" class="form-control myDate required" name = "exam_end_date_in_ad"  data-mask="" data-inputmask="'alias': 'yyyy-mm-dd'" class="form-control" placeholder="Enter Joining date"  value = "{{Input::old('exam_end_date_in_ad')}}">
    	<span class = 'help-block'>@if($errors->has('exam_end_date_in_ad')) {{$errors->first('exam_end_date_in_ad')}} @endif</span>
    </div>

		<div class="form-group ">
          <label>Weightage % for final</label>
           <input id="weightage" name="weightage" class="form-control required" type="text" placeholder="Enter weightage"
value= "{{ (Input::old('weightage')) ? (Input::old('weightage')) : '' }}">
            <span class = 'help-block'>
              @if($errors->has('weightage')) {{$errors->first('weightage')}} @endif
            </span>
        </div>

		<div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'>
			<label for = 'remarks'  class = 'control-label'>Remarks</label>
				
			<textarea class="form-control" placeholder="Enter ..." rows="2" name = "remarks">{{Input::old('remarks')}}</textarea>
		</div>

		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-row'>
			<div subject='col-xs-offset-2 col-xs-10'>
			{{Form::token()}}
			</div>
		</div>
		<div class="form-group">
     		 <button class="btn btn-primary submit-enable-disable" related-form = "backendForm" type="submit">Submit</button>
    	</div>
    	</div>
    </div>
	</form>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
 <script src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
 <script type="text/javascript">
		$(function() {

		    $('.myDate').daterangepicker({
		    	 autoUpdateInput: false,
		        singleDatePicker: true,
		        showDropdowns: true,	        

		    }, 
		    function(start, end, label) {
		        var years = moment().diff(start, 'years');
		    });
		});

		$('.myDate').on('apply.daterangepicker', function(ev, picker){

      		$(this).val(picker.startDate.format('YYYY-MM-DD'));

  		});

  		$('#session_id').change(function(e)
  		{
  			$('#exam_id').html('loading....');
  			$.ajax(
  			{
  				url : '{{URL::route("exam-configuration-api-get-exam-list-from-session-id")}}',
  				method : 'get',
  				data : {'session_id' : $('#session_id').val()}
  			}).success(function(data)
  			{
  				$('#exam_id').html(data);
  			});
  		});

</script>





@stop
