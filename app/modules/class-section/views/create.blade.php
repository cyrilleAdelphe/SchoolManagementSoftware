@extends('include.form-tabs')

@section('custom-css')
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('tab-content')

{{-- $actionButtons --}}

<div class = 'content'>

<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">
		<div class = "content">
			<div class="form-group">
	      <label>Select Session</label>
	      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
	      $selected = Input::has('academic_session_id') ? Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
	    </div>
			
			@foreach($data['classess'] as $class_id => $c)
				<div class = 'form-group'>
					<h4>{{$c}}</h4>
					<input type = "hidden" name = "class_id[]" value = "{{$class_id}}">
					<div>
						@foreach($data['sections'] as $s)
						<input type = "checkbox" name = "sections_of_class_{{$class_id}}[]" value = "{{$s}}" @if(isset($data['classessSections'][$class_id]) && in_array($s, $data['classessSections'][$class_id])) checked @endif>
						 &nbsp;&nbsp;{{$s}}&nbsp;&nbsp;&nbsp;
						@endforeach
					</div>
				</div>
			@endforeach
			<input type = 'hidden' name = 'is_active' value = 'yes'>
			<div class = 'form-row'>
				<div class='col-xs-offset-2 col-xs-10'>
				{{Form::token()}}
				</div>
			</div>

			<div class = 'form-group'>
				<button class="btn btn-success btn-lg btn-flat submit-enable-disable" type="submit" related-form = "backendForm">Submit</button>
			</div>
			
		</div>
	</form>
</div>

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>

<script>
	/**
   * Update the classes and sections according to academic_session_id
  **/
  function updateClassSections()
  {
    var academic_session_id = $('#academic_session_id').val();
        
    if(academic_session_id!=0)
    {
      var current_url = $('#current_url').val();
      current_url += '?academic_session_id=' + academic_session_id;
      window.location.replace(current_url);
    }
  }
   
	$(function() {
		$(document).on('change', '#academic_session_id', updateClassSections);
	});
  

</script>

<script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue',
    increaseArea: '20%' // optional
  });
});
</script>

@stop
