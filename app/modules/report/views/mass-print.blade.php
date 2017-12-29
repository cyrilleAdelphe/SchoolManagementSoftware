@if(count($data))
	@define $json_report_config = json_decode($data[0]->exam_details)
@endif


@if($json_report_config->report_settings->print_type == 'one')
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ $class_name }} {{ $section_code }}</title>
    <link href="{{asset('sms/assets/css/print.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Convergence" rel="stylesheet">
    <style>
    	body{ font-family:arial; font-size: 10px; line-height: 20px; margin:0; padding: 0 }
		table { border-collapse: collapse;}
	    table, td, th {  border: 1px solid #666;} 
	    .myReport{width: 96%; display: block;  overflow: hidden; padding:2% 2%; }
	    .nextDiv{ margin-left: 10% }
	    .topHead{width: 100%; height: auto; overflow: hidden;}
	    .mName{text-align:center; padding: 5px 10px; color: #000; width: 150px; margin: 0 auto; font-size: 20px;}
	    .gradeContent{ width: 100% }
	    .tTitle{padding: 2px; background-color: #ccc; font-weight: bold; font-size: 10px; line-height: 16px;}
	    .sD{ text-align: center; font-size: 10px}
	    .sDs{ text-align:none; padding-left: 3px; font-size: 10px}
	    .remarks{ padding:15px; border: 1px solid #000; display: block; height: 10px; }
	    .mNotes{ width: 50%; float: left }
	    .tData{text-align: center; line-height: 16px; font-size: 10px;}

	    .bHold{ display: block; clear: both; overflow: hidden; margin-top: 20px;  }

	    .aSub{ width: 45%; float: right;  }

      	.rTop{ padding:10px  20px;  border-bottom: 2px solid #000; display: block; overflow: hidden; height: 110px; }

      	.sDet{width: 70%; margin-left: 5%; float: left; display: inline-block;}
      	.sLogo{ width: 20%; float: left; display: inline-block; }

      	.address{ font-size: 16px; line-height: 22px; }

      	.mTot{font-weight: bold;  background-color: #ccc; text-align: center; font-size: 10px}

		@media  print
		{
			body{ font-family:arial; font-size: 12px; line-height: 20px; margin:0; padding: 0;   }
			.pagebreak { page-break-after: always; }
			table, td, th {  border: 1px solid #666 ;}
			table, td, th {  border: 1px solid #666 ;}
			.tTitle{padding: 2px;  background-color: #ccc !important; -webkit-print-color-adjust: exact; font-weight: bold; font-size: 10px; line-height: 16px;}
			.mName{text-align:center; padding: 5px 10px; -webkit-print-color-adjust: exact; color: #000; width: 150px; margin: 0 auto; font-size: 20px;}
			.sD{padding-left: 3px}
			.mTot{font-weight: bold;  background-color: #ccc; -webkit-print-color-adjust: exact; padding: 0 3px; font-size: 10px}

		}
		</style>
  </head>

	<body>
		@foreach($data as $d)
			@define $practical_full_marks_total = 0
			@define $practical_pass_marks_total = 0
			@define $theory_full_marks_total = 0
			@define $theory_pass_marks_total = 0
			@define $combined_marks_total = 0
			@define $practical_marks_total = 0
			@define $theory_marks_total = 0
			@define $combined_marks_total = 0
			
			@define $json = json_decode($d->exam_details)
			
		<div id="reportTemplate" class = "pagebreak">
			<div class="myReport">
				<div class="contentHolder">
					<!-- <div class="topHead">
						<div class="rTop" >
					        <img src="http://sossurkhet.sajiloschool.com/sos-banner.png" height="100" width="auto" />
					    </div> --><!-- rTop ends -->
					    <div class="rTop" >
					        <div class="sLogo">
					          <img src = "{{ Config::get('app.url').'app/modules/settings/config/school_logo' }}" height = "100px" width = auto>  
					        </div>
					        <div class="sDet">
					            <div style="font-size: 18px; padding-top: 10px; margin-left: 100px; line-height: 20px; font-weight: bold; font-family: 'Convergence', sans-serif; ">{{ SettingsHelper::getGeneralSetting('long_school_name') }}</div>
					            <div style="display: block; clear: both; overflow: hidden; margin-left: 100px; ">
					              <div style="float: left; width: 75%">
					                <div class="address">
					                  {{ SettingsHelper::getGeneralSetting('address') }}<br/>
					                  Tel: {{ SettingsHelper::getGeneralSetting('contact') }}&nbsp;&nbsp;&nbsp;Email: {{ SettingsHelper::getGeneralSetting('email') }}&nbsp;&nbsp;&nbsp;Website: {{Config::get('app.url')}}<br/>
					                </div>
					              </div>
					            </div>
					        </div>
					    </div>
						<h3 style="text-align: center; font-size: 20px; font-weight: bold;"> {{ $json->exam_details->exam_name }} </h3>
						<div class="mName"><strong>Grade Sheet</strong></div>
						<h4><strong>Name:</strong>{{ $json->personal_details->student_name }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Class: </strong>{{ $json->personal_details->class }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Section: </strong>{{ $json->personal_details->section }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Roll No: </strong> {{ $json->personal_details->roll }} </h4>
					</div>
				</div>
				<!-- row ends -->

				
				<div class="row">
					<div class="col-sm-12">
						<table class="gradeContent">
							<thead>
				            	<tr>
					                <th rowspan="2" class="tTitle">SN</th>
					                <th rowspan="2" class="tTitle">Subject</th>
					                @if($json_report_config->report_settings->show_cas == 'yes')<th rowspan="2" class="tTitle">CAS</th>@endif
					                <th class="tTitle" colspan="4">Theory</th>
					                <th class="tTitle" colspan="4">Practical</th>				                
					                <th class="tTitle" colspan="2">Th + Pr</th>
					                @if($json_report_config->report_settings->show_cas == 'yes')<th class="tTitle" colspan="2">Cas Sub Combined</th>@endif
					                <th rowspan="2" class="tTitle">Max Marks/Grade<br/> (Pr + Th)</th>
				            	</tr>
				            	<tr>
				            		<th class="tTitle">FM</th>
				            		<th class="tTitle">PM</th>
				            		<th class="tTitle">Obtained Marks</th>
				            		<th class="tTitle">GP</th>
				            		<th class="tTitle">FM</th>
				            		<th class="tTitle">PM</th>
				            		<th class="tTitle">Obtained Marks</th>
				            		<th class="tTitle">GP</th>
				            		@if($json_report_config->report_settings->show_cas == 'yes')<th class="tTitle">Marks</th>
				            		<th class="tTitle">GP</th>@endif
				            		<th class="tTitle">Marks</th>
				            		<th class="tTitle">GP</th>
				            	</tr>
				            </thead>
				            <tbody>
				            @define $i = 0
				            @foreach($json->exam_details->graded_sub_details as $j)
				            	<tr>
				            		<td class="sD">{{++$i}}</td>
				            		<td class="sDs">{{$j->subject_name}}</td>
				            		@if($json_report_config->report_settings->show_cas == 'yes')<td class="sD">{{$j->cas_grade}}</td>@endif
				            		<td class="sD">{{$j->theory_full_marks}}</td>
				            		<?php $theory_full_marks_total += $j->theory_full_marks; ?>
				            		<td class="sD">{{$j->theory_pass_marks}}</td>
				            		<?php $theory_pass_marks_total += $j->theory_pass_marks; ?>
				            		<td class="sD">{{$j->theory_marks}} / {{ $j->theory_grade }}</td>
				            		<?php $theory_marks_total += (int) $j->theory_marks; ?>
				            		<td class="sD">{{ $j->theory_gpa }}</td>

				            		<td class="sD">{{$j->practical_full_marks}}</td>
				            		<?php $practical_full_marks_total += $j->practical_full_marks; ?>
				            		<td class="sD">{{$j->practical_pass_marks}}</td>
				            		<?php $practical_pass_marks_total += $j->practical_pass_marks; ?>
				            		<td class="sD">{{$j->practical_marks}} / {{ $j->practical_grade }}</td>
				            		<?php $practical_marks_total += (int) $j->practical_marks; ?>
				            		<td class="sD">{{ $j->practical_gpa }}</td>

				            		<td class="sD">{{$j->combined_marks}} / {{ $j->combined_grade }}</td>
				            		<td class="sD">{{ $j->combined_gpa }}</td>
									@if($json_report_config->report_settings->show_cas == 'yes')<td class="sD">{{ $j->cas_sub_combined_marks }} / {{ $j->cas_sub_combined_grade }}</td>
				            		<td class="sD">{{ $j->cas_sub_combined_gpa }}</td>@endif
				            		<td class="sD">{{$j->cas_sub_combined_highest_marks}} / {{$j->cas_sub_combined_highest_grade}}</td>
				            		<?php $combined_marks_total += $combined_marks_total + $j->practical_full_marks + $j->theory_full_marks; ?>
				            	</tr>
				            @endforeach
				            <tr>
				            	<td class="mTot"></td>
				            	<td class="mTot">Total</td>
				            	@if($json_report_config->report_settings->show_cas == 'yes')<td class="mTot"></td>@endif
				            	<td class="mTot">{{ $theory_full_marks_total }}</td>
				            	<td class="mTot">{{ $theory_pass_marks_total }}</td>
				            	<td class="mTot">{{ $theory_marks_total }}</td>
				            	<td class="mTot"></td>

				            	<td class="mTot">{{ $practical_full_marks_total }}</td>
				            	<td class="mTot">{{ $practical_pass_marks_total }}</td>
				            	<td class="mTot">{{ $practical_marks_total }}</td>
				            	<td class="mTot"></td>
				            	<td class="mTot"></td>
				            	<td class="mTot"></td>
				            	@if($json_report_config->report_settings->show_cas == 'yes')<td class="mTot">{{ $json->summary->cas_sub_combined_total }}</td>	
				            	<td class="mTot"></td>@endif
				            	<td class="mTot"></td>		            	
				            </tr>
				            </tbody>
						</table>
					</div>
				</div>
				<!-- row ends -->
				<table style="border:0; margin: 15px 0; width: 100%">
					<thead>
						<tr>
							{{-- <td class="tTitle" style="text-align: center;">Rank</td> -}}
							<td class="tTitle" style="text-align: center;">Total Marks</td>
							{{-- <td class="tTitle" style="text-align: center;">Status</td> --}}
							<td class="tTitle" style="text-align: center;">GPA</td>
							<td class="tTitle" style="text-align: center;">Attendance</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							{{-- <td class="tData">{{$d->rank}}</td> --}}
							{{-- <td class="tData">{{$json->summary->cas_sub_combined_total}}</td> --}}
							{{-- <td class="tData">
								<span class= @if($json->summary->status == "Failed") 
				            		"text-red" @else "text-green" @endif>
				              		{{$json->summary->status}}
				              	</span>
	              			</td> --}}
							<td class="tData">
								{{ sprintf("%0.2f", $json->summary->cas_sub_combined_gpa) }}
							</td>
							<td class="tData">
								{{ $d->attendance }}
							</td>
						</tr>
					</tbody>
				</table>

				<div class="remarks"><strong>Remarks:</strong> {{ $d->remarks }}</div>
				<!-- row ends -->

				
				<div class="bHold">
					<table class="mNotes">
							<thead>
								<tr>
									<th class="tTitle">SN</th>
									<th class="tTitle">Interval in Percent</th>
									<th class="tTitle">Grade</th>
									<th class="tTitle">Description</th>
									<th class="tTitle">Grade Point</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="sD">1</td>
									<td class="sD">90 to 100</td>
									<td class="sD">A+</td>
									<td class="sD">Outstanding</td>
									<td class="sD">4.0</td>
								</tr>
								<tr>
									<td class="sD">2</td>
									<td class="sD">80 to below 90</td>
									<td class="sD">A</td>
									<td class="sD">Excellent</td>
									<td class="sD">3.6</td>
								</tr>
								<tr>
									<td class="sD">3</td>
									<td class="sD">70 to below 80</td>
									<td class="sD">B+</td>
									<td class="sD">Very Good</td>
									<td class="sD">3.2</td>
								</tr>
								<tr>
									<td class="sD">4</td>
									<td class="sD">60 to below 70</td>
									<td class="sD">B</td>
									<td class="sD">Good</td>
									<td class="sD">2.8</td>
								</tr>
								<tr>
									<td class="sD">5</td>
									<td class="sD">50 to below 60</td>
									<td class="sD">C+</td>
									<td class="sD">Satisfactory</td>
									<td class="sD">2.4</td>
								</tr>
								<tr>
									<td class="sD">6</td>
									<td class="sD">40 to below 50</td>
									<td class="sD">C</td>
									<td class="sD">Acceptable</td>
									<td class="sD">2.0</td>
								</tr>
								<tr>
									<td class="sD">7</td>
									<td class="sD">30 to below 40</td>
									<td class="sD">D+</td>
									<td class="sD">Partially Acceptable</td>
									<td class="sD">1.6</td>
								</tr>
								<tr>
									<td class="sD">8</td>
									<td class="sD">20 to below 30</td>
									<td class="sD">D</td>
									<td class="sD">Insufficient</td>
									<td class="sD">1.2</td>
								</tr>
								<tr>
									<td class="sD">9</td>
									<td class="sD">0 to below 20</td>
									<td class="sD">E</td>
									<td class="sD">Very Insufficient</td>
									<td class="sD">0.8</td>
								</tr>
							</tbody>
						</table>
						@if(isset($json->exam_details->non_graded_sub_details))
							<?php $chunks = array_chunk((array) $json->exam_details->non_graded_sub_details, 9, true); ?>
							@foreach($chunks as $chunk)
										<table class="aSub">
											<thead>
												<tr>
													<td colspan="3" class="tTitle">Evaluation of Student's Academic Aptitude and Behaviour</td>
												</tr>
								            </thead>
								            <tbody>
								            @define $i = 0
								            @foreach($chunk as $j)
								            	<tr>
								            		<td class="sD">{{++$i}}</td>
								            		<td class="sD">{{$j->subject_name}}</td>
								            		<td class="sD">{{$j->theory_grade}}</td>
								            	</tr>
								            @endforeach
								            </tbody>
										</table>
							@endforeach
						@endif
					</div> <!-- bHold ends -->
				<div style="margin-top: 50px">
					<table class="btm" style="width: 100%; border:0">
						<thead>
							<tr>
								<td style=" text-align: center;border:0"> <strong>{{ ExamConfiguration::where('id', Input::get('exam_id', 0))->pluck('result_publish_date') }}</strong></td>
								<td style=" text-align: center;border:0"> <strong> </strong></td>
								<td style=" text-align: center;border:0"> <strong></strong></td>
	                            <td style=" text-align: center;border:0"> <strong></strong></td>
							</tr>
							<tr>
								<th style=" text-align: center;border:0"> <br/>---------------------------------</th>				
								<th style=" text-align: center;border:0"> <br/>---------------------------------</th>
	                            <th style=" text-align: center;border:0"> <br/>---------------------------------</th>
	                            <th style=" text-align: center;border:0"> <br/>---------------------------------</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style=" text-align: center;border:0"> <strong>Date</strong></td>
								<td style=" text-align: center;border:0"> <strong>Class Teacher </strong></td>
								<td style=" text-align: center;border:0"> <strong>Exam Co-ordinator</strong></td>
	                            <td style=" text-align: center;border:0"> <strong>Principal</strong></td>
							</tr>
						</tbody>
					</table>
				</div> 
			</div>
		</div>
		@endforeach
		<!-- reportTemplate div ends -->
		
	</body>

</html>

@else
	Print Two
@endif