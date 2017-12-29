
@if(count($subjects))
		
			@foreach($subjects as $s)
			<div class = "form-group">
				<label>
					{{$s->subject_name}}:
				</label>
				<br/>
					@foreach($teachers as $t)

						<input type = "checkbox" name = "_subject_teacher_{{$s->id}}[]" value = "{{$t->admin_details_id}}" @if(isset($selectedSubjectTeacher[$s->id]) && in_array($t->admin_details_id, $selectedSubjectTeacher[$s->id])) checked @endif> &nbsp;&nbsp;{{$t->name}}&nbsp;&nbsp;&nbsp;
					@endforeach
			</div>
			@endforeach
			<script>
jQuery(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue',
    increaseArea: '20%' // optional
  });
});
</script>
<br/>
			<div class = "form-group">
				<button type = "submit" class = "btn btn-success btn-lg btn-flat submit-enable-disable" related-form="backendForm">Assign</button>
				<a href= "{{URL::route('subject-list')}}" class = "btn btn-danger btn-lg btn-flat">Cancel</a>
				{{Form::token()}}
			</div>
		
@else
		<div class="alert alert-warning alert-dismissable">
	  		<h4><i class="icon fa fa-warning"></i>No Data Found</h4>
	 	</div>

@endif
