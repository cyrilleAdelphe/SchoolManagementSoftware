<!-- Detail Modal -->
<div id="details" class="modal fade" role="dialog">
  <div class="modal-dialog">
  	<!-- Modal content-->
	    <div class="modal-content">

	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Edit details</h4>
	      </div>

	      <form method = "post" action = "{{URL::route($module_name.'-edit-post', array($data['data']->id))}}" id = "backendForm" enctype = "multipart/form-data">
	      		<div class="modal-body">

					<div class="form-group {{ $errors->has('dob_in_ad') ? 'has-error' : '' }}">
		      			<label>DOB:</label>   
		      			@define $dob_in_ad = DateTime::createFromFormat('Y-m-d', $data['data']->dob_in_ad)                    
					 	<input type="text" class="form-control" name = "dob_in_ad" value="{{ $dob_in_ad ? $dob_in_ad->format('m/d/Y') : '' }}" />
					 	<span class = 'help-block'>@if($errors->has('dob_in_ad')) {{$errors->first('dob_in_ad')}} @endif</span>
		           </div>
		          
		          <div class="form-group {{ $errors->has('current_address') ? 'has-error' : '' }}">
		        		<label>Current address:</label>
		            <input id="caddress" class="form-control" type="text" name="current_address" value="{{ $data['data']->current_address}}" />
		            <span class = 'help-block'>@if($errors->has('current_address')) {{$errors->first('current_address')}} @endif</span>
		          </div>

		          <div class="form-group {{ $errors->has('permanent_address') ? 'has-error' : '' }}">
		        		<label>Permanent address:</label>
		            <input id="caddress" class="form-control" type="text" name = "permanent_address" value= "{{ $data['data']->permanent_address}}" />
		            <span class = 'help-block'>@if($errors->has('permanent_address')) {{$errors->first('permanent_address')}} @endif</span>
		          </div>
		          <div class="form-group">
		          	  <input type = 'hidden' name = 'student_name' value= '{{$data['data']->student_name}}' class = 'form-control'>
				      <input type = 'hidden' name = 'sex' value= '{{$data['data']->sex}}' class = 'form-control'>
				      <input type = 'hidden' name = 'guardian_contact' value= '{{$data['data']->guardian_contact}}' class = 'form-control'>
				      <input type = 'hidden' name = 'secondary_contact' value= '{{$data['data']->secondary_contact}}' class = 'form-control'>
				      <input type = 'hidden' name = 'email' value= '{{$data['data']->email}}' class = 'form-control'>
				      <input type = 'hidden' name = 'registered_session_id' value= '{{$data['data']->registered_session_id}}' class = 'form-control'>
				      <input type = 'hidden' name = 'registered_class_id' value= '{{$data['data']->registered_class_id}}' class = 'form-control'>
				      <input type = 'hidden' name = 'registered_section_code' value= '{{$data['data']->registered_section_code}}' class = 'form-control'>
				      <input type = 'hidden' name = 'photo' value= '' class = 'form-control'>
				      <input type = "hidden" name = "original_photo" value = "{{$data['data']->photo}}">
				      <input type = "hidden" name = "role" value = "student">
					  <input type = "hidden" name = "old_session_id" value = "{{$data['data']->registered_session_id}}">
					  <input type = "hidden" name = "id" value = "{{$data['data']->id}}">
					  <input type = "hidden" name = "unique_school_roll_number" value = "{{$data['data']->unique_school_roll_number}}">
					  <input type = 'hidden' name = 'is_active' value = 'yes'>
					  <input type = "hidden" name = "redirect_back" value = 'yes' />
					</div>							
			    </div>
			    {{Form::token()}} 
			    <div class="modal-footer">
				        <button class="btn btn-danger pull-left btn-flat" data-dismiss="modal" type="button">Close</button>
						<button class="btn btn-success btn-flat submit-enable-disable" type="submit" rekated-form="backendForm">Save changes</button>
				</div>	      
		    </form>	     
	    </div>
  </div>
</div>

<script src= "{{asset('backend-js/submit-enable-disable.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
    $('input[name="dob_in_ad"]').daterangepicker({
    		singleDatePicker: true,
        showDropdowns: true,
        startDate: $('input[name="dob_in_ad"]').val(),
        endDate: ''
        
    }, 
    function(start, end, label) {
        var years = moment().diff(start, 'years');
    });
});
</script>