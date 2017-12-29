<div class = "row">
	<div class = "col-md-2">
		<b>Class</b>
	</div>
	<div class = "col-md-2">
		<b>Section</b>
	</div>
	<div class = "col-md-2">
		<b>Student</b>
	</div>
	<div class = "col-md-3">
		<b>Discount %</b>
	</div>
</div>
@foreach($details as $d)
<div class = "row">
	<div class = "col-md-3">
		<p><select class = "class_id">-- Select Session First --</select></p>
	</div>
	<div class = "col-md-3">
		<p><select class = "section_id">-- Select Class First --</select></p>
	</div>
	<div class = "col-md-3">
		<p><select class = "student_id">-- Select Section First --</select></p>
	</div>
	<div class = "col-md-3">
		<p><input type = "number" step = "0.01"</p>
	</div>
</div>
@endforeach