<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
   <style>
    body,ul,li{ font-family:arial; font-size: 10px; line-height: 14px; margin: 0 ; padding:0; list-style: none;}
    table { border-collapse: collapse;}
        table, td, th {  border: 1px solid #666;}
        .mainHolder{display: block;}
        .monthlyBill{width: 90%; display: block; overflow: hidden; margin: 0 5%; margin-top: 30px  }
        .fee-head{height: 100px; margin-bottom: 10px; display: block; overflow: hidden;border-bottom: 1px solid #333   }
        .fee-detail{ width: 30%; float: left; }
        .student-title{ width:40%; float: left; font-weight: bold  }
        .student-details{ width: 58%; float: right; }
        .fee-items{ width: 65%; float: right; }
        .fHead{font-weight: bold; background-color: #ddd !important; text-align: left; padding-left: 5px; }
        .fContent{text-align: left; padding-left: 5px}

        .fee-body{clear: both; display: block;}

        .fee-summary{ display: block; clear: both; }

        .previous_month{display: block; clear: both;}

        .sImg{float: left;  display: inline-block; width: 15% }
        .sAdd{float: left; margin-left: 15px; font-size: 16px; line-height: 25px; width: 75% }
        .sAdd:first-line{font-size: 25px; font-weight: bold; line-height: 25px;}
        .acc{ padding-top: 150px; font-size: 16px; text-align: center; display: block; clear: both; overflow: hidden; }

        .myNote{ margin: 0 ; padding: 0; margin-top: 20px; }

        .myNote ul li{ list-style: none; line-height: 20px; font-size: 12px; }

    @media  print
    {
      .monthlyBill{ height:510px }
      body,ul,li{ font-family:arial; font-size: 12px; line-height: 16px; list-style: none; padding: 0; margin: 0 }
      .fHead{ font-weight: bold; background-color: #ddd !important; -webkit-print-color-adjust: exact; text-align: left; padding-left: 5px; }
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
	<div class="fee-head">
		<div class="sImg">
		  <img src = "{{ Config::get('app.url').'app/modules/settings/config/school_logo' }}" height = "90px" width = auto>   
		</div>
		<div class="sAdd">     
		  {{ SettingsHelper::getGeneralSetting('long_school_name') }}<br/>
		  <strong>{{ SettingsHelper::getGeneralSetting('address') }}</strong><br/>
		  <strong>Phone:</strong>{{ SettingsHelper::getGeneralSetting('contact') }}<br/>
		</div>
	</div><!-- head ends -->
	<h1>Notice Bill of {{ BillingDiscountOrganization::where('id', Input::get('organization_id'))->pluck('organization_name') }}</h1>
	@define $sum = 0
	@define $previous_sum = 0
	@if($return_data)
		@foreach($return_data as $student_id => $data)
		
		@if(isset($data['current_month']))
			@foreach($data['current_month'] as $d )
			

					@define $invoice_details = json_decode($d->invoice_details, true)
					
					<h4>{{$d->invoice_group_id}}</h4>

				<table>
					@define $i = 0
					@foreach($invoice_details['fees'] as $f)
						@if($i == 0)
							<tr>
								<td>SN</td>
								<td>Student Name</td>
								<td>Fee Title</td>
								<td>Amount</td>
							</tr>
						@endif
						
						<tr>
							<td>{{++$i}}</td>
							<td>{{$f['recipient']}}</td>
							<td>{{$f['fee_title']}}</td>
							<td>{{$f['fee_amount']}}</td>
						</tr>
					@endforeach
				</table>
				<table>
					<tr>
						<td><strong>Without Tax: </strong></td>
						<td>{{$invoice_details['summary']['sum_without_tax']}}</td>
					</tr>
					<tr>
						<td><strong>Tax: </strong></td>
						<td>{{$invoice_details['summary']['tax']}}</td>
					</tr>

					<tr>
						<td><strong>Total: </strong></td>
						<td>{{$d->invoice_balance}}</td>
					</tr>
					<tr>
						<td><strong>Recevied Amount: </strong></td>
						<td>{{ $d->received_amount }}</td>
					</tr>
					<?php $sum = $sum + $d->invoice_balance - $d->received_amount ?>
				</table>

		@endforeach
		@endif
		<table>
			<tr>
				<td>Total Balance of current month: </td>
				<td>{{ $sum }}</td>
			</tr>
		</table>
		
		
		@if(isset($data['previous_month']))
			@foreach($data['previous_month'] as $d )

				@define $previous_sum += $d->invoice_balance - $d->received_amount

			@endforeach
		@endif
		<table>
			<tr>
				<td>Previous Balance: </td>
				<td>{{ $previous_sum }}</td>
			</tr>
		</table>

	@endforeach
	<table>
		<tr>
			<td><strong>Total: </strong></td>
			<td>{{ $sum + $previous_sum }}</td>
		</tr>
	</table>

@else
<h1>No Remaining Fees Found</h1>

@endif
</body>
</html>

