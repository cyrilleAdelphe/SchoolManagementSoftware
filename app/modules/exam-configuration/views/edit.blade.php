@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

@if($data)
	<div class = 'container'>
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>
		<div class = 'form-group @if($errors->has("subject_name")) {{"has-error"}} @endif'>
			<label for = 'exam_name'  class = 'control-label'>Exam Name</label>
				
			<input id="exam_name" name="exam_name" class="form-control required" type="text" placeholder="Enter exam name"
                value= "{{$data->exam_name}}">
            <span class = 'help-block'>
              @if($errors->has('exam_name')) {{$errors->first('exam_name')}} @endif
            </span>
		</div>

		<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
			<label for = 'session_id'  class = 'control-label'>Session</label>
				
			@define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();
              <select class = "form-control" id = "session_id" name = "session_id">
                @foreach($sessions as $s)
                @if($s->id == $data->session_id)
                  @define $current_session = $s->id
                @endif
                <option value = "{{$s->id}}" @if($s->id == $data->session_id) selected @endif>{{$s->session_name}}</option>
                @endforeach
              </select>
		</div>

		<div class = 'form-group @if($errors->has("parent_exam_id")) {{"has-error"}} @endif'>
			<label for = 'parent_exam_id'  class = 'control-label'>Parent Exam</label>
				
			<select id = 'parent_exam_id' class = "form-control" name = "parent_exam_id">
			@define $exams = ExamConfiguration::where('session_id', $current_session)->lists('exam_name', 'id');

			<option value = "0">Root</option>
			@foreach($exams as $exam_id => $exam_name)
			<option value = "{{$exam_id}}" @if($exam_id == $data->parent_exam_id) selected @endif>{{$exam_name}}</option>
			@endforeach
			</select>
		</div>

		<div class="form-group">
            <label>Start date:</label>
			<div class="input-group">
		        <div class="input-group-addon">
		          <i class="fa fa-calendar"></i>
		        </div>
		        <input type="text" name = "exam_start_date_in_ad" data-mask="" data-inputmask="'alias': 'yyyy/mm/dd'" class="form-control" placeholder="Enter Joining date" value = "{{$data->exam_start_date_in_ad}}">
		    </div>
		</div>

		<div class="form-group">
          <label>End date:</label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name = "exam_end_date_in_ad"  data-mask="" data-inputmask="'alias': 'yyyy/mm/dd'" class="form-control" placeholder="Enter Joining date"  value = "{{$data->exam_end_date_in_ad}}">
          </div>
        </div>

		<div class="form-group ">
          <label>Weightage % for final</label>
           <input id="weightage" name="weightage" class="form-control required" type="text" placeholder="Enter weightage"
value= "{{$data->weightage}}">
            <span class = 'help-block'>
              @if($errors->has('weightage')) {{$errors->first('weightage')}} @endif
            </span>
        </div>

		<div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'>
			<label for = 'remarks'  class = 'control-label'>Remarks</label>
				
			<textarea class="form-control" name = "remarks" placeholder="Enter ..." rows="2">{{$data->remarks}}</textarea>
		</div>

		<span><input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif>No</span>
		
		<a href = "{{URL::route($module_name.'-list')}}" class = "btn btn-default">Cancel</a>
		<div class = 'form-row'>
			<div subject='col-xs-offset-2 col-xs-10'>
			{{Form::token()}}
			</div>
		</div>
		<div class="form-group">
      <button type = "submit" class = "btn btn-info" value = "edit">Edit</button>
      <input type = "hidden" name = "id" value = "{{$data->id}}">
    </div>
		</form>
	</div>
@else
	<h1>No Record Found</h1>
@endif

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "javascript/text"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "javascript/text"></script>

<script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>

    <script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
      $(function () {
        //Datemask yyyy/mm/dd
        $("#datemask").inputmask("yyyy/mm/dd", {"placeholder": "yyyy/mm/dd"});
        $("[data-mask]").inputmask();  

        //Timepicker
        $(".timepicker").timepicker({
          showInputs: false
        });      
      });

      $('#session_id').change(function(e)
  		{
  			var exam_id = $('.exam_id').val();
  			$('#exam_id').html('loading....');
  			$.ajax(
  			{
  				url : '{{URL::route("exam-configuration-api-get-exam-list-from-session-id")}}',
  				method : 'get',
  				data : {'session_id' : $('#session_id').val(), 'default_exam_id' : exam_id}
  			}).success(function(data)
  			{
  				$('#exam_id').html(data);
  			});
  		});
    </script>


@stop


