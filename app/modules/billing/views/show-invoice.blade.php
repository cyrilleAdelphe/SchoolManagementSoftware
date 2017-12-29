<html>
<head>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" /> 
    <!-- Theme style -->
    <link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />
     <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>  
    <style>
    	table{margin-bottom: 10px !important}
    	table>thead>tr>th,table>tbody>tr>td{ padding:4px !important; font-size: 14px }
    </style>
</head>
<body>
<div class="container">

			@if($data['data'])
			@define $invoice_details = json_decode($data['data']->invoice_details, true)
			<div class="happy-info" style="margin-bottom: 10px">
		        Details of <strong>{{$invoice_details['personal_details']['name']}} </strong>| @if($invoice_details['personal_details']['group'] == 'student')
					Roll number : {{$invoice_details['personal_details']['roll_number']}}<br>
					Details: {{ $data['data']->invoice_group_id  }} 
				@endif
				<!-- Billing-v1-changed-made-here -->
				Invoice Number : <strong>{{$data['data']->financial_year}}-{{$data['data']->invoice_number}}</strong>
				<!-- Billing-v1-changed-made-here -->
		    </div>
			
		    	<table class = 'table table-striped table-hover table-bordered'>
			    	<thead>
			    		<tr>
			    			<th>SN</th>
			    			<th>Title</th>
			    			<th>Amount</th>
			    			@if($invoice_details['personal_details']['group'] == 'organization')
							<th>Recipient</th>
							@endif
			    		</tr>
			    	</thead>
			    	<tbody>
			    	@define $i = 0
					@foreach($invoice_details['fees'] as $fee)
						<tr>
							<td>{{++$i}}</td>
							<td>{{BillingHelperController::removeQuotesAndUnderScore($fee['fee_title'])}}</td>
							<td>{{$fee['fee_amount']}}</td>
							@if($invoice_details['personal_details']['group'] == 'organization')
							<td>{{$fee['recipient']}}</td>
							@endif
						</tr>
					@endforeach
					</tbody>
				</table>
				
				@if($invoice_details['personal_details']['group'] == 'student')
					@if(isset($invoice_details['discount']))
						
						@foreach($invoice_details['discount'] as $d)
							<div class="info-bar" >
								Discount Details
							</div>
							<table class = "table table-striped table-hover table-bordered">
								<tr>
									<td><strong>Org. Name</strong></td>
									<td>{{$d['organization_name']}} - {{$d['discount_title']}}</td>
								</tr>
								<tr>
									<td><strong>Title</strong></td>
									<td>{{$d['fee_title']}}</td>
								</tr>
								<tr>
									<td><strong>Discount amount</strong></td>
									<td>{{$d['discount_amount']}}</td>
								</tr>
							</table>	
						@endforeach	
											
					@endif
				@endif
				<div class="danger-bar" >
					Total Details
				</div>
				<table class = "table table-striped table-hover table-bordered">
					<tr>
						<td><strong>Taxable Amount</strong></td>
						<td>{{$invoice_details['summary']['taxable_amount']}}</td>
					</tr>
					<tr>
						<td><strong>Untaxable Amount</strong></td>
						<td>{{$invoice_details['summary']['untaxable_amount']}}</td>
					</tr>
					<tr>
						<td><strong>Sum without tax</strong></td>
						<td>{{$invoice_details['summary']['sum_without_tax']}}</td>
					</tr>
					<tr>
						<td><strong>Tax</strong></td>
						<td>{{$invoice_details['summary']['tax']}}</td>
					</tr>
					<tr>
						<td class="text-green"><strong>Total</strong></td>
						<td class="text-red" style="font-size: 18px">{{$invoice_details['summary']['total']}}</td>
					</tr>
				</table>

				<table>
					<tr>
						<td>Note: </td>
						<!-- Billing-v1-changed-made-here -->
						@if(isset($data['data']->note))
						<!-- Billing-v1-changed-made-here -->
						@define $notes = explode('\n', $data['data']->note)
						<td>@foreach($notes as $n)
							<p>{{$n}}</p>
							@endforeach
						</td>
						@endif
					</tr>
				</table>
			

				<p>Status: <strong>{{$data['data']->is_cleared}}</strong></p>
				<a class="btn btn-lg btn-flat btn-danger pull-right">Paid Amount: {{$data['data']->received_amount}}</a>
			
			@else
				<h1>Invalid invoice</h1>
			@endif

</div>
</body>
</html>