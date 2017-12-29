
<?php $data = BillingFee::with('studentFee')->where('id', $id)->first(); ?>

@if($data)
<form action = "{{URL::route('billing-edit-fee-post', $id)}}" method = "post">
<div class = "content">
	<div class = "row">
		<div class = "col-md-12">
			<label>Fee Category: </label>
			<input type = "text" name = "fee_category" value = "{{$data->fee_category}}">
		</div>
	</div>

	<div class = "row">
		<div class = "col-md-12">
			<label>Tax applicable: </label>
			<input type = "radio" name = "tax_applicable" value = "yes" @if($data->tax_applicable == 'yes') checked @endif>Yes <input type = "radio" name = "tax_applicable" value = "no" @if($data->tax_applicable == 'no') checked @endif>No
		</div>
	</div>

	<div class = "row">
		<div class = "col-md-12">
		  <label>Fee Type: </label>
          <select class="form-control" id="edit_fee_type" name = "fee_type">
            <option value="onetime" @if($data->fee_type == 'onetime') selected @endif>One time</option>
            <option value="recurring" @if($data->fee_type == 'recurring') selected @endif>Recurring</option>
            <option value="baishak" @if($data->fee_type == 'baishak') selected @endif>Baishak</option>
            <option value="jestha" @if($data->fee_type == 'jestha') selected @endif>Jestha</option>
            <option value="ashad" @if($data->fee_type == 'asahd') selected @endif>Ashad</option>
            <option value="shrawan" @if($data->fee_type == 'shrawan') selected @endif>Sharwan</option>
            <option value="bhadra" @if($data->fee_type == 'bhadra') selected @endif>Bhadra</option>
            <option value="ashwin" @if($data->fee_type == 'ashwin') selected @endif>Ashwin</option>
            <option value="kartik" @if($data->fee_type == 'kartik') selected @endif>Kartik</option>
            <option value="mangsir" @if($data->fee_type == 'mangsir') selected @endif>Mangshir</option>
            <option value="poush" @if($data->fee_type == 'poush') selected @endif>Poush</option>
            <option value="magh" @if($data->fee_type == 'magh') selected @endif>Margh</option>
            <option value="falgun" @if($data->fee_type == 'falgun') selected @endif>Falgun</option>
            <option value="chaitra" @if($data->fee_type == 'chaitra') selected @endif>Chaitra</option>
          </select>
		</div>
	</div>

	<div class = "row">
		<div class = "col-md-12">
			<label>Description : </label>
			<input type = "text" name = "description" value = "{{$data->description}}">
		</div>
	</div>

	
	@define $count = count($data->studentFee)
	@define $i = 1
	<?php $sessions = DB::table(Classes::getTableName())
						->join(AcademicSession::getTableName(), AcademicSession::getTableName().'.id', '=', 'academic_session_id')
						->where(AcademicSession::getTableName().'.is_active', 'yes')
						->where(Classes::getTableName().'.is_active', 'yes')
						->select('academic_session_id', 'session_name', Classes::getTableName().'.id as class_id')
						->get();
	$temp = [];
	foreach($sessions as $s)
	{
		$temp[$s->class_id]['session_name'] = $s->session_name;
		$temp[$s->class_id]['academic_session_id'] = $s->academic_session_id;
	}

	$sessions = $temp;

	unset($temp);

	?>
		@define $count = count($data->studentFee) - 1
		@foreach($data->studentFee as $index => $details)
		<div class = "fee_student_details">
			<div class = "row">
				<div class = "col-md-2">
				
					@if(isset($sessions[$details->class_id]['session_name']))
					{{$sessions[$details->class_id]['session_name']}}
					<input type = "hidden" name = "academic_session_id[]" value = "{{$sessions[$details->class_id]['academic_session_id']}}" class = "academic_session_id">
					@endif
				</div>
				<div class = "col-md-2">
					Class: {{Classes::where('id', $details->class_id)->pluck('class_name')}}
					<input type = "hidden" name = "class_id[]" value = "{{$details->class_id}}" class = "class_id">
				</div>
				<div class = "col-md-2">
					Section: {{Section::where('id', $details->section_id)->pluck('section_code')}}
					<input type = "hidden" name = "section_id[]" value = "{{$details->section_id}}" class = "section_id">
				</div>
				<div class = "col-md-2">
					Amount: {{$details->fee_amount}}
					<input type = "hidden" name = "amount[]" value = "{{$details->fee_amount}}" class = "amount">
				</div>
				<div class = "col-md-2">
					@define $excluded_student_ids = explode(',', $details->excluded_student_id)

					<?php $excluded_student_names = DB::table(StudentRegistration::getTableName())
														->join(Student::getTableName(), Student::getTableName().'.student_id', '=', StudentRegistration::getTableName().'.id')
														->where('current_class_id', $details->class_id)
														->whereIn(StudentRegistration::getTableName().'.id', $excluded_student_ids)
														->select('student_name', 'current_roll_number', StudentRegistration::getTableName().'.id')
														->get() ;

					?>
					Excluded Students: 
					@foreach($excluded_student_names as $name)
						
						 ( {{$name->current_roll_number}} ) {{$name->student_name}}
						 <input type = "hidden" name = "student_id[{{$details->class_id}}][{{$details->section_id}}][]" value = "{{$name->id}}"  class = "student_id">
							
					@endforeach
				</div>
				<div class = "col-md-2">
					<button type = "button" class = "btn btn-danger add-edit-module-btn-edit-details">Edit</button>
					<button type = "button" class = "btn btn-danger add-edit-module-btn-remove-details">Remove</button>
					
					<button type = "button" class = "btn btn-danger add-edit-module-btn-add-details">Add</button>
					
				</div>
			</div>
		</div>

		
		@endforeach
</div>		
<input type = "submit" class = "btn btn-success" value = "edit">		
{{Form::token()}}
</form>
@else
	<h1>No data Found</h1>
@endif

