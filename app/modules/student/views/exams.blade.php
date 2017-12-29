@if (count($unpaid_fees) && $role != 'superadmin')
	<div class="alert alert-warning alert-dismissable">
		<h4>
			<i class="icon fa fa-warning"></i>
			You have unpaid fees. Clear your dues to view exam reports.
		</h4>
	</div>
@else

<div class = "row">
	<div class = "col-md-12">
		<h4>Exam Report Of Individual Exams</h4>
		<table id="pageList" class="table table-bordered table-striped">
			<thead>
				<tr>
			      <th>SN</th>
			      <th>Exam</th>
			      <th>Percentage</th>
			      <th>CGPA</th>
			      <th>Rank</th>
			      <th>Action</th>
			    </tr>
		  </thead>
		  <?php 
		  	$i = 1;
		  	$single_url = URL::route('report-single');
		  ?>
		  <tbody>
		  	@foreach($exams as $exam)
		    <tr>
					<td>{{ $i++ }}</td>
					<td>{{ json_decode($exam->exam_details)->personal_details->exam_name }}</td>
					<td>{{ $exam->percentage }}</td>
					<td>{{ $exam->cgpa }}</td>
					<td>{{ $exam->rank }}</td>
					<td>
						<a data-toggle="tooltip" title="View detail" href = "{{URL::route('report-mass-print')}}?exam_id={{$exam->exam_id}}&student_id={{$exam->student_id}}&class_id={{$exam->class_id}}&section_id={{$exam->section_id}}" class="btn btn-info btn-flat" @if(!AccessController::checkPermission('report', 'can_view')) disabled @endif>
							<i class="fa fa-fw fa-eye"></i>
						</a>
					</td> 
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
<div class = "row">
	<div class = "col-md-12">
		<h4>Exam Report of Weighted Final Exams</h4>

		<table id="pageList" class="table table-bordered table-striped">
			<thead>
				<tr>
			      <th>SN</th>
			      <th>Exam</th>
			      <th>Percentage</th>
			      <th>CGPA</th>
			      <th>Rank</th>
			      <th>Action</th>
			    </tr>
		  </thead>
		  <?php 
		  	$i = 1;
		  	$single_url = URL::route('report-single');
		  ?>
		  <tbody>
		  	@foreach($final_exams as $exam)
		    <tr>
					<td>{{ $i++ }}</td>
					<td>{{ json_decode($exam->exam_details)->personal_details->exam_name }}</td>
					<td>{{ $exam->percentage }}</td>
					<td>{{ $exam->cgpa }}</td>
					<td>{{ $exam->rank }}</td>
					<td>
						<a data-toggle="tooltip" title="View detail" href = "{{URL::route('report-mass-print')}}?exam_id={{$exam->exam_id}}&student_id={{$exam->student_id}}&class_id={{$exam->class_id}}&section_id={{$exam->section_id}}&is_final=yes" class="btn btn-info btn-flat" @if(!AccessController::checkPermission('report', 'can_view')) disabled @endif>
							<i class="fa fa-fw fa-eye"></i>
						</a>
					</td> 
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endif
		