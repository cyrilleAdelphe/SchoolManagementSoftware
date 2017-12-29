<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	 <style>
	 	body,ul,li{ font-family:arial; font-size: 10px; line-height: 14px; margin: 0 ; padding:0; list-style: none;}
	 	table { border-collapse: collapse;}
        table, td, th {  border: 1px solid #666;}

        .mainHolder{padding: 15px;}
        .monthlyBill{width: 100%; display: block; overflow: hidden;  }
        .invoiceFor{ line-height: 40px; font-weight: bold;  }
        .fHead{font-weight: bold; background-color: #ddd !important; text-align: left; padding-left: 5px}
        .fContent{text-align: left; padding-left: 5px}
        .tSum{display: block; clear: both; border-bottom: 1px solid #666; width: 70%; line-height: 16px; height: 16px; float: right;}
        .nextDiv{ margin-top: 10% }
        .fee-head, .fee-body{clear: both; display: block;}
        .fee-detail{ width: 30%; float: left; }
        .fee-items{ width: 65%; float: right; }
        .fee-head{height: 130px; margin-bottom: 15px; display: block; overflow: hidden;  }
        .school-logo{ width: 10%; overflow: hidden; float: left; height: 100px; }
        .school-detail{float: left; margin-left: 2%; width: 88%}
        .schoolName{ font-size: 18px; line-height: 30px; font-weight: bold; }

        .student-title{ width:40%; float: left; font-weight: bold  }
        .student-details{ width: 58%; float: right; }
		@media print
		{
			body,ul,li{ font-family:arial; font-size: 12px; line-height: 16px; list-style: none; padding: 0; margin: 0 }
			.fHead{ font-weight: bold; background-color: #ddd !important; -webkit-print-color-adjust: exact; text-align: left; padding-left: 5px }
			.pagebreak {  page-break-after: always !important;  }
		 	table, td, th {  border: 1px solid #666 !important;}
		}

	</style>
	<script  src="https://code.jquery.com/jquery-2.2.4.min.js"  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<script>
		$( document ).ready(function() {
			$(".monthlyBill:nth-child(even)").addClass("nextDiv");
		});
	</script>
</head>
<body>
	@if($return_data)
		
		<?php $block_data = array_chunk($return_data, 2); 
		?>
		
		@foreach($block_data as $return_data)
			<div class="mainHolder">			
				@foreach($return_data as $student_id => $data)

				<?php $final_sum = 0; $a = 0; ?>
				@if(isset($data['current_month']))
					@define $is_student_details_written = false
					@foreach($data['current_month'] as $d )

						<?php $paid = $d->received_amount;?>
						@define $invoice_details = json_decode($d->invoice_details, true)
						
							<div class="monthlyBill"> 
								
								<div class="fee-head">
									
								</div><!-- head ends -->
								<div class="fee-body">
									
									<div class="fee-detail">
										@if(!$is_student_details_written)
											<div class="student-title">
												<ul>
													<li>Fiscal Year:</li>
													<li>Date:</li>
													<li>Issued by:</li>
													<li>Invoice No:</li>
													<li>Reg. no:</li>
													<li>Name:</li>
													<li>Class:</li>
													<li>Roll no:</li>
													<li>Address:</li>
													<li>Parents:</li>
													
													
												</ul>
											</div>
											<div class="student-details">
												<?php $date_and_class[0] = $d->year_in_bs;  ?>
												<?php $date_and_class[1] = $d->month_in_bs;  ?>
												
												<ul>
													<li>{{$d->financial_year}}</li>
													<li>@if(isset($date_and_class[0])){{$date_and_class[0]}} @endif - @if(isset($date_and_class[1])){{$date_and_class[1]}} @endif</li>
													<li>{{$d->created_by}}</li>
													<li>{{$d->invoice_number}}</li>
													<li>{{$d->username}}</li>
													<li>{{$invoice_details['personal_details']['name']}}</li>
													<li>{{$d->class_section}}</li>
													<li>{{$invoice_details['personal_details']['roll_number']}}</li>
													<li> {{$d->current_address}}</li>
													<li>{{$d->guardian_name}}</li>
													
													
												</ul>
											</div>
											@define $is_student_details_written = true
										@endif
									</div><!-- fee detail ends -->
									<div class="fee-items">
										<table  width="100%">
											<thead>
											<tr>
												<th class="fHead">SN</th>
												<th class="fHead">Title</th>
												<th class="fHead">Amount</th>
													@if($invoice_details['personal_details']['group'] == 'organization')
														<th>Recipient</th>
													@endif
											</tr>
											</thead>
											<tbody>
												@define $i = 0
												<?php $sum = 0;?>
												@foreach($invoice_details['fees'] as $fee)

													<tr>
														<td class="fContent">{{++$i}}</td>
														<td class="fContent">{{BillingHelperController::removeQuotesAndUnderScore($fee['fee_title'])}}</td>
														<td class="fContent">{{$fee['fee_amount']}}</td>
														@if($invoice_details['personal_details']['group'] == 'organization')
														<td class="fContent">{{$fee['recipient']}}</td>
														@endif
													</tr>
											
												<?php $sum =$fee['fee_amount'] + $sum ; ?>	
												@endforeach

												
													<tr>
														<th></th>
														<th class="fContent"><strong>Total</strong></th>
														<th class="fContent"><strong>{{$sum}}</strong></th>
													</tr>
												
											</tbody>
										</table>
										<br/>


										@if($invoice_details['personal_details']['group'] == 'student')
											@if(isset($invoice_details['discount']))
												<table width="100%">
													<thead>
														<tr>
															<th colspan="2" class="fHead">Discount Details</th>
														</tr>
													</thead>
													<tbody>
														@foreach($invoice_details['discount'] as $discount)
															
															<tr>
																<td class="fContent"><strong>Org. Name</strong></td>
																<td class="fContent">{{$discount['organization_name']}} - {{$discount['discount_title']}}</td>
															</tr>
															<tr>
																<td colspan="2" class="fContent">
																	<strong>Title</strong> - {{$discount['fee_title']}} - Rs. {{$discount['discount_amount']}}
																</td>
															</tr>
														@endforeach
													</tbody>
												</table>
												<br/>		
											@endif
										@endif
										<table width="100%">
											<thead>
												<tr>
													<th colspan="4" class="fHead">Total Amounts</th>
												</tr>
											</thead>
											<tbody>
											<tr>
												<td class="fContent">
													<strong>Taxable</strong> - {{$invoice_details['summary']['taxable_amount']}}
												</td>
												<td class="fContent">
													<strong>Untaxable</strong> - {{$invoice_details['summary']['untaxable_amount']}}
												</td>
												<td class="fContent">
													<strong>Tax</strong> - {{$invoice_details['summary']['tax']}}
												</td>
												<td class="fContent">
													<strong>Total</strong> - <?php $a = $invoice_details['summary']['total'] - $paid;	echo $a; ?>
												</td>
											</tr>
											</tbody>
											<?php $final_sum =$d->invoice_balance + $final_sum - $paid; ?>
										</table>
									</div><!-- fee items ends -->
								</div><!-- fee body ends -->
							</div><!-- monthly bill ends -->	
					@endforeach
					<br/>
					<div class="tSum">
						<div style="width: 70%; float: left"><strong>Total Of This Month:</strong></div>
						<div style="width: 30%; float: left">{{$final_sum}}</div>
					</div>				
				@endif
				@if(isset($data['previous_month']))

					@define $previous_due = 0
					@foreach($data['previous_month'] as $d)
						<?php $previous_due += $d->invoice_balance - $d->received_amount; ?>
						@define $invoice_details_previous = json_decode($d->invoice_details, true)
								<?php $final_sum = $d->invoice_balance + $final_sum - $d->received_amount; ?>
					@endforeach
					<div class="tSum">
						<div style="width: 70%; float: left"><strong>Previous Balance:</strong></div> 
						<div style="width: 30%; float: left">{{$previous_due}}</div>
					</div>
				@endif
					<div class="tSum">
						<div style="width: 70%; float: left"><strong>Grand Total:</strong></div>
						<div style="width: 30%; float: left">{{$final_sum}}</div>
					</div>
									
						
							@endforeach
							</div><!-- main holder -->	

						<div class="pagebreak">&nbsp;</div>

					@endforeach


	@else
	<h1>Invalid invoice</h1>
	@endif
	
</body>
</html>