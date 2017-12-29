<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ $class_name }} {{ $section_code }}</title>
    <link href="{{asset('sms/assets/css/print.css')}}" rel="stylesheet" type="text/css" />
    <style>
		@media print
		{
		.pagebreak { page-break-after: always; }
		}
		</style>

    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
    <!-- FontAwesome 4.3.0 -->
    <link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="{{asset('sms/assets/css/ionicons.min.css')}}" rel="stylesheet" type="text/css" />    
    <!-- Theme style -->
    <link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />

    <!-- bootstrap wysihtml5 - text editor -->
    <link href="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- jQuery 2.1.4 -->
    <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>    
    <!-- Underscore JS (for javascript templating) -->
  </head>

	<body>
		<?php $first = true; ?>
		@foreach($data as $d)
			@define $json = json_decode($d->exam_details)
			@if($first)
				@define $no_of_exams = count((array) $json->exam_details)
				@define $first = false
			@endif
		<div id="reportTemplate" class = "pagebreak">
			<div class="row">
				<div class="col-sm-12">
					<br/><br/><br/><br/><br/><br/><br/><br/><br/>
					<h3 style="text-align: center;"> {{ $json->personal_details->exam_name }} </h3>
					<h3 style="text-align:center"><strong>Grade Sheet</strong></h3>
					<h4><strong>Name:</strong> {{ $json->personal_details->student_name }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Class: </strong>{{ $json->personal_details->class }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Section: </strong>{{ $json->personal_details->section }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Roll No: </strong> {{ $json->personal_details->roll }} </h4>
				</div>
			</div>
			<!-- row ends -->
			<div class="row">
				<div class="col-sm-12">
					<table id="pageList" class="table table-bordered table-striped">
						<thead>
			            	<tr>
				                <th>SN</th>
				                <th>Subject</th>
				                <th colspan={{$no_of_exams}}>CAS</th>
				                <th colspan={{$no_of_exams + 3}}>Practical</th>
				                <th colspan={{$no_of_exams + 3}}>Theory</th>
				                <th>Total (Pr + Th)</th>
				                <th>Max Marks/Grade (Pr + Th)</th>
			            	</tr>
			            	<tr>
			            		<th></th>
			            		<th></th>
			            		@foreach($json->exam_details as $exam_id => $k)
			            		<th>{{$k->exam_name}}</th>
			            		@endforeach
			            		<th>FM</th>
			            		<th>PM</th>
			            		@foreach($json->exam_details as $exam_id => $k)
			            		<th>{{$k->exam_name}} ({{$k->weight}}%)</th>
			            		@endforeach
			            		<th>Total (100%)</th>
			            		<th>FM</th>
			            		<th>PM</th>
			            		@foreach($json->exam_details as $exam_id => $k)
			            		<th>{{$k->exam_name}} ({{$k->weight}}%)</th>
			            		@endforeach
			            		<th>Total (100%)</th>
			            		<th></th>
			            		<th></th>
			            	</tr>
			            </thead>
			            <tbody>
			            @define $i = 0
			            @foreach($json->exam_summary as $subject_id => $j)
			            	<tr>
			            		<td>{{++$i}}</td>
			            		<td>{{$j->subject_name}}</td>
			            		@foreach($json->exam_details as $exam_id => $k)
			            		
			            		<td>@if(isset($k->graded_sub_details->$subject_id->weighted_cas_grade)) {{$k->graded_sub_details->$subject_id->weighted_cas_grade}} @else * @endif</td>

			            		@endforeach
			            		<td>{{$j->practical_full_marks}}</td>
			            		<td>{{$j->practical_pass_marks}}</td>
			            		@foreach($json->exam_details as $exam_id => $k)
			            		<td>@if(isset($k->graded_sub_details->$subject_id->weighted_practical_marks)) {{$k->graded_sub_details->$subject_id->weighted_practical_marks}} @else NA @endif</td>
			            		@endforeach
			            		<td>{{$j->practical_marks}}</td>
			            		<td>{{$j->theory_full_marks}}</td>
			            		<td>{{$j->theory_pass_marks}}</td>
			            		@foreach($json->exam_details as $exam_id => $k)
			            		<td>@if(isset($k->graded_sub_details->$subject_id->weighted_theory_marks)) {{$k->graded_sub_details->$subject_id->weighted_theory_marks}} @else * @endif</td>
			            		@endforeach
			            		<td>{{$j->theory_marks}}</td>
			            		<td>{{$j->combined_marks}}</td>
			            		<td>{{$j->combined_highest_marks}} / {{$j->combined_highest_grade}}</td>
			            	</tr>
			            @endforeach
			            </tbody>
					</table>
				</div>
			</div>
			<!-- row ends -->
			<div class="row">
				<div class="col-sm-12">
					<table class="table">
            <tr>
              <td><strong>Rank :</strong></td>
              <td>{{$d->rank}}</td>
            </tr>
            
            <tr>
              <td><strong>Total :</strong></td>
              <td>{{$json->summary->cas_sub_combined_total}}</td>
            </tr>
            <tr>
              <td><strong>Status :</strong></td>
              <td>
              	<span class= @if($json->summary->status == "Failed") 
            		"text-red" @else "text-green" @endif>
              		{{$json->summary->status}}
              	</span>
              </td>
            </tr>
            <tr>
              <td><strong>Percentage :</strong></td>
              <td>{{ sprintf("%0.2f", $json->summary->cas_sub_combined_percentage) }}</td>
            </tr>
           
            
            
            <tr>
              <td><strong>Grade :</strong></td>
              <td>
                {{$json->summary->cas_sub_combined_grade}}
              </td>
            </tr>
            
            
            <tr>
              <td><strong>Grade Point Average :</strong></td>
              <td>{{ sprintf("%0.2f", $json->summary->cas_sub_combined_gpa) }}</td>
            </tr>

            <tr>
              <td><strong>Remarks :</strong></td>
              <td>{{ $d->remarks }}</td>
            </tr>

            <tr>
              <td><strong>Attendance :</strong></td>
              <td>{{ $d->attendance }}</td>
            </tr>
            
          </table>
				</div>
			</div>

			@foreach($json->exam_details as $exam_id => $k)
				@if(isset($k->non_graded_sub_details))
					<h3>{{$k->exam_name}}</h3>
					<?php $chunks = array_chunk((array) $k->non_graded_sub_details, 2, true); ?>
					@foreach($chunks as $chunk)
					<table class="table table-bordered table-striped">
						<tr>
							
						</tr>
						<tr>
							<th>SN</th>
							<th>Non Graded Sub Name</th>
							<th>Grade</th>
						</tr>
						@define $i = 0
						@foreach($chunk as $subject_id => $sub_details)
						<tr>
							<td>{{++$i}}</td>
							<td>{{$sub_details->subject_name}}</td>
							<td>{{$sub_details->weighted_theory_grade}}</td>
						</tr>
						@endforeach
					</table>
					@endforeach
				@endif
			@endforeach
			<!-- row ends -->
			
		</div>
		@endforeach
		
		
	</body>

</html>