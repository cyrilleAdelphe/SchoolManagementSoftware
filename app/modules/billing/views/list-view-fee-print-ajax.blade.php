<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
   <style>
    body,ul,li{ font-family:arial; font-size: 10px; line-height: 14px; margin: 0 ; padding:0; list-style: none;}
    table { border-collapse: collapse;}
        table, td, th {  border: 1px solid #666;}
        .stdData{ line-height: 16px}
        .stdData tr, .stdData td{border:none !important; vertical-align:top;}
        .firstData{font-weight: bold;}
        .mainHolder{display: block;}
        .monthlyBill{width: 90%; display: block; overflow: hidden; margin: 0 5%; margin-top: 30px  }
        .fee-head{height: 100px; margin-bottom: 10px; display: block; overflow: hidden;border-bottom: 1px solid #333   }
        .fee-detail{ width: 30%; float: left; }
        .student-title{ width:30%; float: left; font-weight: bold  }
        .student-details{ width: 65%; float: right; }
        .fee-items{ width: 65%; float: right; }
        .fHead{font-weight: bold; background-color: #ddd !important; text-align: left; padding-left: 5px; }
        .fContent{text-align: left; padding-left: 5px}

        .fee-body{clear: both; display: block;}

        .fee-summary{ display: block; clear: both; }

        .previous_month{display: block; clear: both;}

        .sImg{float: left;  display: inline-block; width: 15% }
        .sAdd{float: left; margin-left: 60px; font-size: 12px; line-height: 25px; width: 70% }
        .sAdd:first-line{font-size: 16px; font-weight: bold; line-height: 25px;}
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
  <!-- <script>
    $( document ).ready(function() {
      $(".monthlyBill:nth-child(even)").addClass("nextDiv");
    });
  </script> -->
</head>
<body>
<?php
  $block_data = array_chunk($block_data, 2, true);
?>

@define $current_month_total = 0
@define $current_month_paid = 0
@define $previous_month_remaining = 0
@define $has_data = false               

@foreach($block_data as $return_datas)
<!--  <div class="mainHolder"> -->
    @foreach($return_datas as $return_data)
    @define $has_data = true
    <div class = "monthlyBill">
      <div class="fee-head">
        <div class="sImg">
          <img src = "{{ Config::get('app.url').'app/modules/settings/config/school_logo' }}" height = "60px" width = auto>   
        </div>
        <div class="sAdd">     
          {{ SettingsHelper::getGeneralSetting('long_school_name') }}<br/>
          <strong>{{ SettingsHelper::getGeneralSetting('address') }}</strong><br/>
          <strong>Phone:</strong>{{ SettingsHelper::getGeneralSetting('contact') }}<br/>
          Email: {{ SettingsHelper::getGeneralSetting('email') }}&nbsp;&nbsp;&nbsp;Website: {{Config::get('app.url')}}<br/>
        </div>
      </div><!-- head ends -->
      <div class="fee-body">
        <div class="fee-detail">
            <table class="table stdData" style="border:none; width: 100%">
              <tr>
                <td class="firstData">Name:</td>
                <td>{{$return_data['student_detail']->student_name}} {{$return_data['student_detail']->last_name}}</td>
              </tr>
              <tr>
                <td class="firstData">Roll:</td>
                <td>{{$return_data['student_detail']->current_roll_number}}</td>
              </tr>
              <tr>
                <td class="firstData">Class:</td>
                <td>{{$class}} {{$section}}</td>
              </tr>
              <tr>
                <td class="firstData">Username:</td>
                <td>{{$return_data['student_detail']->username}}</td>
              </tr>
              <tr>
                <td class="firstData">Issued:</td>
                <td>{{$issued_date}}</td>
              </tr>
              <tr>
                <td class="firstData">Fee of:</td>
                <td>{{$year}} {{getMonth($month_index)}}</td>
              </tr>
              <tr>
                <td class="firstData">Parents:</td>
                <td>{{$return_data['student_detail']->guardian_name}}</td>
              </tr>
            </table>

          <div class="acc">
            --------------------------------<br/>
            <strong>Accountant</strong>
          </div>
        </div><!-- fee details ends -->
        @if(count($return_data['current_month']))
        @define $tax = 0
        @define $taxable_amount = 0
        @define $untaxable_amount = 0
        <div class="fee-items">
          <table  width="100%">
            <thead >
              <tr>
                <th class="fHead">SN</th>
                <th class="fHead">Title</th>
                <th class="fHead">Amount</th>
              </tr>
            </thead>
              <tbody>
                @define $i = 0
                @define $current_month_total = 0
                @define $current_month_paid = 0

                @foreach($return_data['current_month'] as $d)
                  @define $current_month_total += $d->invoice_balance
                  @define $current_month_paid += $d->received_amount
                  @define $invoice_details = json_decode($d->invoice_details, true)
                  @if(isset($invoice_details['fees']))
                    @foreach($invoice_details['fees'] as $fee)
                      @if($fee['fee_amount'])
                        <tr>
                          <td>{{++$i}}</td>
                          <td>{{BillingHelperController::removeQuotesAndUnderScore($fee['fee_title'])}}</td>
                          <td>{{$fee['fee_amount']}}</td>
                        </tr>
                      @endif
                    @endforeach
                  @endif
                @define $tax += $invoice_details['summary']['tax']
                @define $taxable_amount += $invoice_details['summary']['taxable_amount']
                @define $untaxable_amount += $invoice_details['summary']['untaxable_amount']
                @endforeach
              
                @foreach($return_data['current_month'] as $d)
                  @define $invoice_details = json_decode($d->invoice_details, true)
                  @if(isset($invoice_details['discount']) && !empty($invoice_details['discount']))
                    <tr>
                      <th class="fHead" colspan="3">Discount Details</th>
                    </tr>
                    @foreach($invoice_details['discount'] as $discount)
                      <tr>
                        <td>{{$discount['organization_name']}} - {{$discount['discount_title']}}</td>
                        <td>
                          <strong>Title</strong> - {{$discount['fee_title']}}
                        </td>
                        <td>Rs. {{$discount['discount_amount']}}<td/>
                      </tr>
                    @endforeach
                  @endif
                @endforeach
              </tbody>
          </table>
          <table  width="100%">
            <tr>
              <td>Notes</td>
            </tr>
            @foreach($return_data['current_month'] as $d)
              @if(strlen($d->note)) 
                @define $notes = explode('\n', $d->note)
              @else
                @define $notes = []
              @endif
              @if(empty($notes))
              @else
                <tr>
                  <td><ol>
                      @foreach($notes as $note)
                          <li>{{$note}}</li>
                      @endforeach
                      </ol>
                  </td>
                </tr>
              @endif
            @endforeach
          </table>
          <table width="100%" style="margin-top: 10px">
            <thead>
              <tr>
                <th colspan="4" class="fHead">Total - Current month</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fContent">
                  <strong>Taxable</strong> - {{$taxable_amount}}
                </td>
                <td class="fContent">
                  <strong>Untaxable</strong> - {{$untaxable_amount}}
                </td>
                <td class="fContent">
                  <strong>Tax</strong> - {{$tax}}
                </td>
                <td class="fContent">
                  <strong>Total</strong> - {{$current_month_total}}
                </td>
              </tr>
            </tbody>
          </table>
          <br/>     
          @endif
              @define $previous_month_remaining = 0
          @if(count($return_data['previous_month']))

              @foreach($return_data['previous_month'] as $d)
                <?php $previous_month_remaining += ($d->invoice_balance - $d->received_amount); ?>
              @endforeach
              <div style="font-size: 14px; font-weight: bold; line-height: 20px"><strong>Previous Due:&nbsp;&nbsp;&nbsp;</strong>Rs.
 &nbsp;{{$previous_month_remaining}}</div>
          @endif
              <div style="font-size: 14px; font-weight: bold; line-height: 20px"><strong>Grand Total: &nbsp;&nbsp;&nbsp;</strong> Rs.&nbsp;<span class ="pra_grand_total">{{$current_month_total - $current_month_paid + $previous_month_remaining}}</span></div>
              <ul class="myNote">
                <li>* Payment must be made within 15 days of bill issuance.</li>
                <li>* This bill must be prepared at the time of payment</li>
                <li>* Delay will cost late fine as per school rule.</li>
              </ul>
        </div>
      </div><!-- fee body ends -->
    </div>
    @endforeach
  <!-- </div> -->
  <!-- <div class="pagebreak">&nbsp;</div> -->
@endforeach

@if(!$has_data)
  <h1>No Remaining Dues Found</h1>
@endif
<script>
$(document).ready(function()
{
	var span = $('.pra_grand_total');
	span.each(function(index)
	{
		var current_element = $(this);
		var total = current_element.html();
		if(total == 0)
		{
			current_element.parent().parent().parent().parent().remove();
		}
	});
	
	var monthly_bill_elements = $('.monthlyBill');
	var i = 1;
	monthly_bill_elements.each(function(index)
	{
		if(i%2==0)
		{
			$(this).addClass('pagebreak');
		}
		i = i+1;
	});
});
</script>
</body>
</html>

<?php

	function getMonth($month_index)
	{
		$month_index = (int) $month_index;
		$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		return $months[$month_index-1];
	}
?>