@extends('pdr.views.tabs')

@section('custom-css')
<link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />

@stop

@section('tab-content')
<form method = "post" action = "{{URL::route('pdr-create-post')}}" id = "backendForm">
	<div class="row">
		
		<div class="col-sm-3">
	      	<div class="form-group @if($errors->has('pdr_date')) has-error @endif">
	        	<label>Date:</label>
	          	<div class="input-group">
	           		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
	          		<input name="pdr_date" id="date" data-toggle="tooltip" title="dd/mm/yyyy" type="text" data-mask="" data-inputmask="'alias': 'yyyy-mm-dd'" class="form-control" value=" {{Input::old('pdr_date') ? Input::old('pdr_date') : date('Y-m-d') }}" data-mask />
	          		<span class = 'help-block'>@if($errors->has('pdr_date')) {{$errors->first('pdr_date')}} @endif</span>
	          </div>
	        </div>
	    </div>

		<div class="col-sm-3">
		    <div class="form-group">
		    <label>Select Session</label>
			    {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id', 
			    $selected = 
			    Input::old('session_id') ?
			    Input::old('session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
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
	<div id = "ajax-content"></div>		
</form>
@stop

@section('custom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
		$(function() {
		    $('input[name="pdr_date"]').daterangepicker({
		        singleDatePicker: true,
		        format: 'YYYY-MM-DD',
		        showDropdowns: true,
		        endDate: ''
		    }, 
		    function(start, end, label) {
		        var years = moment().diff(start, 'years');
		    });
		});
    </script>
<script>
$(function()
	{
		function updateClassIdList(session_id, default_class_id)
		{
			$('#class_id').html('loading....');
			$.ajax({
				'method' : 'get',
				'data' : {'session_id' : session_id, 'default_class_id' : default_class_id},
				'url' : '{{URL::route("ajax-get-class-ids-from-session-id-html")}}'
			}).done(function(data)
			{
				$('#class_id').html(data);
				updateSectionIdList($('#class_id').val(), $('#section_id').val());
			});
		}

		function updateSectionIdList(class_id, default_section_id)
		{
			$('#section_id').html('loading....');
			$.ajax({
				'method' : 'get',
				'data' : {'class_id' : class_id, 'default_section_id' : default_section_id},
				'url' : '{{URL::route("ajax-get-section-ids-from-class-id-html")}}'
			}).done(function(data)
			{
				$('#section_id').html(data);
				updatePdrList();
			});
		}

		function updatePdrList()
		{
			//alert('here');
			$('#ajax-content').html('loading....');

			var session_id = $('#session_id').val();
			var class_id = $('#class_id').val();
			var section_id = $('#section_id').val();
			var date = $('#date').val();
			$.ajax({
				'method' : 'get',
				'data' : {'class_id' : class_id, 'session_id' : session_id, 'pdr_date':date, 'section_id' : section_id},
				'url' : "{{URL::route('pdr-partials-create-get')}}"
			}).done(function(data)
			{
				$('#ajax-content').html(data);
			});
		}

		updateClassIdList($('#session_id').val(), $('#class_id').val());

		$('#session_id').change(function(e)
		{

			var default_class_id = $('#class_id').val();
			var session_id = $('#session_id').val();

			updateClassIdList($('#session_id').val(), $('#class_id').val());

		});

		$('#class_id').change(function(e)
		{

			var default_section_id = $('#section_id').val();
			var class_id = $('#class_id').val();

			updateSectionIdList($('#class_id').val(), $('#section_id').val());

		});

		$('#date').change(function(e)
		{
			updatePdrList();
		});
	});

</script>
@stop

