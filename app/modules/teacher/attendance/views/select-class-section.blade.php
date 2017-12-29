@extends('teacher.attendance.views.tabs')


@section('custom-css')
<link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />  
@stop

@section('tab-content')
	<div class="row">
		<div class="col-sm-3">
      <div class="form-group">
        <label>Date:</label>
          <div class="input-group">
           	<div class="input-group-addon">
            	<i class="fa fa-calendar"></i>
          	</div>
          	<input name="date" id="date" data-toggle="tooltip" title="dd/mm/yyyy" type="text" data-mask="" data-inputmask="'alias': 'dd/mm/yyyy'" class="form-control" @if(isset($date)) value="{{$date}}" @endif />
          </div>
        </div>
    </div>

		<div class="col-sm-3">
	    <div class="form-group">
	      <label>Select Session</label>
	      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
	        $selected = 
	          Input::has('academic_session_id') ?
	          Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
	    </div>
	  </div>

	  <div class="col-sm-3">
	    <div class="form-group">
	      <label>Select class</label>
	      <select name="class_id" id="class_id" class="form-control">
					<option value="0">--Select Session First--</option>
					
				</select>
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

	</div>
	
          	@if($msg != '')
          	<p style="color:red;" align="center">{{ $msg }}</p>
          	@endif

	{{-- <div class="row">
    <div class="col-md-12 col-xs-12 col-sm-12">
      <div class="form-group">
        <a class="btn btn-success"  href="#">
          <i class="fa fa-fw fa-search"></i>

          <div id = "studentList">
          	
	  			</div>
        </a>
      </div>
    </div>
	</div> --}}

	<div id="attendanceForm">
	</div>
		
	<input type="hidden" id="class_ajax" value="{{URL::route('ajax-get-teacher-classes')}}" />

	<input type="hidden" id="section_ajax" value="{{URL::route('ajax-get-classes-section-from-teacher-id')}}" />
		
		
@stop

@section('custom-js')
	<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

	<script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script>

	<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

	<script src="{{asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
  
  <script type="text/javascript">
    $(function () {

      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
      });

    });
  </script>

  <!-- Datepicker  scripts -->
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>

  <script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>

  @if(CALENDAR != 'BS')
  <script type="text/javascript">
    $(function () {
      //Datemask dd/mm/yyyy
      $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
      $("[data-mask]").inputmask();  

      //Timepicker
      $(".timepicker").timepicker({
        showInputs: false
      });      
    });
  </script>
  @endif

	<script type="text/javascript">
		/*
		 * Update the student list according to the class and section provided
		 */
		function updateStudentList()
		{
			var class_id = $('#class_id').val();
			var section_id = $('#section_id').val();

			if(class_id==0 || section_id==0)
			{
				$('#studentList').html('select class / section');
				return;
			}

			var query_url = "{{URL::route('ajax-attendance-get-students-post')}}";
			$.post(query_url,
				{
					'class_id':class_id,
					'section_id':section_id
				},
				function(data,status) 
				{
					if(status)
					{
						$('#studentList').html(data);
					}
				}
			);
		}	

		/*
		 * Update the student attendance form according to the class and section provided
		 */
		function updateAttendanceForm()
		{
			//TODO: a lot of code matches with updateStudentList, take care of it
			var class_id = $('#class_id').val();
			var section_id = $('#section_id').val();
			var date = $('#date').val();

			if(class_id==0 || section_id==0)
			{
				$('#studentList').html('select class / section');
				return;
			}

			$('#attendanceForm').html('<img src="{{asset('sms/assets/img/Loading_icon.gif')}}" />');
			var query_url = "{{URL::route('ajax-attendance-get-attendance-form-post')}}";
			$.post(query_url,
				{
					'class_id' : class_id,
					'section_id' : section_id,
					'date' :  date
				},
				function(data,status) 
				{
					if(status)
					{
						$('#attendanceForm').html(data);
					}
				}
			);
		}

	</script>


	<script type="text/javascript">
		$(function() {//same as $(document).ready(function(){...})
						
			//this condition is required when the class is already selected at the beginning of document (this may happen when back is pressed)
			if($('#academic_session_id').val() != 0)
			{
				updateClassList();
				updateSectionList();
			}

			$(document).on('change', '#academic_session_id', updateClassList);

	    $("#class_id").change(updateSectionList);

	    $("#section_id").change(function() {
	    	updateStudentList();
	    	updateAttendanceForm();
	    });

	    $('#date').keyup(function(e) {
				if(e.which == 13) {
					updateAttendanceForm();
				}
			});

	    $(document).on('click', "#PraSave", function(e){
	    	e.preventDefault();
	    	$('#formSave').append('<input type = "hidden" name = "pushNotification" value = "no">');
	    	$("#attendanceFormDate").val($("#date").val());
				$('#formSave').submit();
	    });

	    $(document).on('click', "#PraSaveAndSendNotification", function(e){
	    	e.preventDefault();
				$('#formSave').append('<input type = "hidden" name = "pushNotification" value = "yes">');
				$("#attendanceFormDate").val($("#date").val());
				$('#formSave').submit();
	    });

		});
	</script>

	
@stop