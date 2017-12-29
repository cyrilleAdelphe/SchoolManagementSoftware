<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ $class_name }} {{ $section }}</title>
    <link href="{{asset('sms/assets/css/print.css')}}" rel="stylesheet" type="text/css" />
    <style>
		@media print
		{
		.pagebreak { page-break-before: always; }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
  </head>

	<body>
		@define $nepali_months = array('Baishakh', 'Jestha', 'Asar', 'Shrawan', 'Bhadra', 'Aswin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra')
	<div id = 'reportTemplate'>
		@foreach($student as $s)
			  <div class="row">
			    <div class="col-sm-6">
			  		<div class="form-group">
			  			<label>Monthly:</label>
			        {{$s['monthly_fee']}}
			  			
			  		</div>

			      @if(isset($s['examination']))
			      <div class="form-group">
			        <label>{{ $s['examination']['exam_name'] }}:</label>
			        {{$s['examination']['amount']}}
			      </div>
			      @endif

			      @if(isset($s['misc_class_fees']))
				      @foreach($s['misc_class_fees']['title'] as $index => $title)
				  		<div class="form-group">
				  			<label>{{$title}}:</label>
				        {{$s['misc_class_fees']['amount'][$index]}}
				       
				  		</div>
				      @endforeach
				  @endif

			      @if(isset($s['misc_student_fees']))
				      @foreach($s['misc_student_fees']['title'] as $index => $title)
				  		<div class="form-group">
				  			<label>{{$title}}:</label>
				        {{$s['misc_student_fees']['amount'][$index]}}
				       
				  		</div>
				      @endforeach
				  @endif

			      <div class="form-group">
			        <label>Transportation Fee:</label>
			        {{$s['transportation_amount']}}
			      </div>

			       <div class="form-group">
			        <label>Hostel Fee:</label>
			        {{$s['hostel']}}
			      </div>

			      @if(isset($s['scholarship']['monthly']))
			      <div class="form-group">
			        <label>Monthly Scholarship:</label>
			       
			        Rs. {{$s['scholarship']['monthly']['amount']}} ({{$s['scholarship']['monthly']['percent']}}%)
			      </div>
			      @endif

			      @if(isset($s['scholarship']['transportation']))
			      <div class="form-group">
			        <label>Transportation Scholarship:</label>
			       Rs. {{$s['scholarship']['transportation']['amount']}} ({{$s['scholarship']['transportation']['percent']}}%)
			      </div>
			      @endif

			      @if(isset($s['scholarship']['hostel']))
			      <div class="form-group">
			        <label>Hostel Scholarship:</label>
			       Rs. {{$s['scholarship']['hostel']['amount']}} ({{$s['scholarship']['hostel']['percent']}}%)
			      </div>
			      @endif

			      @if(isset($s['taxes']['type']))
				      @foreach($s['taxes']['type'] as $index => $tax)
				      <div class="form-group">
				        <label>{{ ucfirst($tax) }} Tax:</label>
				        {{ $s['taxes']['amount'][$index] }}
				      </div>
				      @endforeach
			      @endif      		
			    </div>
			    <div class="col-sm-6">
			      <div class="form-group">
			        <h3>Total: <span class="text-red"> {{$s['payment']['fee_amount']}} </span></h3>            
			      </div>
			      <div class="form-group">
			        <label>Received:</label>
			        {{$s['payment']['received_amount']}}
			      </div>

			      <div class="form-group">
			        <label>Is Paid:</label>
			        {{$s['payment']['is_paid']}}
			      </div>
			  
			    </div>
			  </div><!-- row ends -->
		

			<div class="row">
			  <div class="col-sm-12">
			    <div class="main-head">
			      Other Dues
			    </div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-sm-12">
			      <table id="pageList" class="table table-bordered table-striped">
			        <thead>
			          <tr>
			            <th>SN</th>
			            <th>Month</th>
			            <th>Amount</th>
			            <th>Received</th>
			            <th>Status</th>
			          </tr>
			        </thead>
			        <tbody>
			          @define $i=1
			          @if(isset($s['previous_dues']['month']))
				          @foreach($s['previous_dues']['month'] as $index => $month)
				          <tr>
				            <td>{{$i++}}</td>
				            <td>{{$nepali_months[$month-1]}}</td>
				            <td>{{$s['previous_dues']['fee_amount'][$index]}}</td>
				            <td>{{$s['previous_dues']['received_amount'][$index]}}</td>
				            <td>
				              @if($s['previous_dues']['is_paid'][$index]=='yes')
				                <span class="text-green">Paid</span>
				              @else
				                @if ($s['previous_dues']['received_amount'][$index])
				                  <span class="text-yellow">Partially Paid</span>
				                @else
				                  <span class="text-red">Unpaid</span>
				                @endif
				              @endif
				            </td>
				          </tr>
				          @endforeach
				       @endif
			          
			        </tbody>
			      </table>
			  </div>
			</div><!-- row ends -->
		@endforeach
	</div>
		<!-- reportTemplate div ends -->

		{{-- <script type="text/javascript">
		$(function() {
			var data = {{ json_encode($data) }};
			var class_name = "{{ $class_name }}";
			var section_code = "{{ $section_code }}";
			
			var reportTemplate = $('#reportTemplate').html()
			reportTemplate = reportTemplate.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
			var compiledReportTemplate = _.template(reportTemplate);
			
			var report = '';

			for (var student_number = 0; student_number < data.length; student_number++) {
				if (!data[student_number]) {
					continue;
				}

				var substitution = {
					class_name: class_name,
					section_code: section_code,
					student_name: data[student_number][0].student_name,
					username: data[student_number][0].username,
					rank: data[student_number][0].rank,
					total_marks: data[student_number][0].total_marks,
					status: data[student_number][0].status,
					percentage: data[student_number][0].percentage,
					overall_grade: data[student_number][0].rank,
					cgpa: data[student_number][0].cgpa,
					remarks: data[student_number][0].remarks,
				};

				for (var i = 0; i < data[student_number].length; i++) {
					substitution["subject_name" + i] = data[student_number][i].subject_name;
					substitution["full_marks" + i] = data[student_number][i].full_marks;
					substitution["pass_marks" + i] = data[student_number][i].pass_marks;
					substitution["marks" + i] = data[student_number][i].marks;
					substitution["grade" + i] = data[student_number][i].grade;
					substitution["grade_point" + i] = data[student_number][i].grade_point;
				}
				
				student_report = compiledReportTemplate(substitution);

				if (student_number == 0) {
					report += student_report;
				} else {
					var newReport = $('<div>').attr('class', 'pagebreak');
					newReport.html(student_report);
					report += newReport[0].outerHTML;
				}
			}

			$('body').html(report);
			window.print();

		});
		</script> --}}

		
	</body>

</html>