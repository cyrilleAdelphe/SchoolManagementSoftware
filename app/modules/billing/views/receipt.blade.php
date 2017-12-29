<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="https://fonts.googleapis.com/css?family=Playball" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Convergence" rel="stylesheet">
    <style>
      body,ul,li,table,td,tr{margin: 0; padding: 0; font-family: arial; font-size: 12px; list-style: none;}
      .rHolder{ display: block; clear: both; }
      .rTop{ padding:15px  20px; background-color: #eee; border-bottom: 2px solid #000; height: 100px }
      .rr{ padding: 5px; background-color: #fff; border-radius: 15px; border:2px solid #000; display: block; overflow: hidden  }
      .sCopy{ float: left; width: 25%; display: none;}
      .cCopy{  width: 100%; float: left; margin-bottom: 35px}
      .cDetail{ float: left; width: 95% }
      .uT{float: left; width: 40%; font-weight: bold; line-height: 18px;}
      .sDetail{ padding:10px; padding-left: 20px }
      .cDetail{padding: 10px; font-size: 20px; line-height: 30px; font-family: 'Playball', cursive;}
      .sut{line-height: 25px; font-size: 16px; font-weight: bold;  width: 30%; float: left;}
      .sB{ font-weight: bold; font-size: 14px; padding-top: 15px; clear: both; }
      .suD{ font-size: 16px; width: 70%; float: left; line-height: 25px  }
      .sUt{ float: right; border: 2px solid #000; background-color: #fff; border-radius: 10px; padding: 10px; line-height: 18px; font-weight: bold; width: 50% }
      .address{line-height: 20px; font-size: 14px}
      .mDet li{ float: left; font-family: 'Playball', cursive; font-size: 20px; line-height: 30px  }

      .footNote li{float: left; margin:0 2%; text-align: center; margin-top: 15px;font-family: 'Convergence', sans-serif; line-height: 25px; font-size: 14px;  }
      .fig{ border-top: 1px solid #000; font-size: 16px }

      @media print {
        .rTop {
          background-color: #eee !important;
          -webkit-print-color-adjust: exact; 
      }}
    </style>
  </head>
  <body onload="window.print()">

@if($data)
  @for($i = 0; $i < 3; $i++)
  <div class="cCopy">
        <div class="rTop" >
              
          <div style="font-size: 25px; line-height: 35px; font-weight: bold; font-family: 'Convergence', sans-serif; "><span class = "schoolLogo">
      <img src = "{{ Config::get('app.url').'app/modules/settings/config/school_logo' }}" height = "50px" width = auto>
   </span>{{ SettingsHelper::getGeneralSetting('long_school_name') }}</div>
          <div style="display: block; clear: both; overflow: hidden;">
            <div style="float: left; width: 45%">
              <div class="address">
                {{ SettingsHelper::getGeneralSetting('address') }}<br/>
                Tel: {{ SettingsHelper::getGeneralSetting('contact') }}
              </div>
              <div style="font-size: 10px; font-weight: bold; padding-top: 5px; ">Payment Receipt</div>
            </div>
            <div style="float: right; width: 45%; margin-top: 10px">
              <ul class="sUt">
                <li><strong>Receipt No:</strong> {{$data->financial_year}}-{{$data->receipt_no}}</li>
                <li><strong>Date:</strong> {{(new DateConverter)->ad2bs($data->received_on)}}</li>
              </ul>
            </div>
          </div>
        </div><!-- rTop ends -->
        <div class="cDetail" style="padding-left: 20px">
          Received with Thanks From 
            <div style="font-size: 25px; display: inline-block;"> @if($data->received_from == 'student')
              {{StudentRegistration::where('id', $data->received_id)
                    ->pluck('student_name')}}
        {{StudentRegistration::where('id', $data->received_id)
                    ->pluck('last_name')}}
        @elseif($data->received_from == 'organization')
          <!-- Billing-v1-changed-made-here -->
          Paid By: {{BillingDiscountOrganization::where('id', $data->received_id)
                        ->pluck('organization_name')}}
          <!-- Billing-v1-changed-made-here -->
        @else
          {{ $data->received_name }}
      @endif
      </div>
      <br/>
      
      @if($data->received_from  == 'student') 
        
        
        @define $current_session = HelperController::getCurrentSession();
        @define $class_table = Classes::getTableName()
        @define $student_table = Student::getTableName()
        <?php $details = DB::table($student_table)    
                  ->join($class_table, $class_table.'.id', '=', $student_table.'.current_class_id')
                  ->where('current_session_id', $current_session)
                  ->where('student_id', $data->received_id)
                  ->select('current_roll_number', 'class_name', 'current_section_code')
                  ->first();

        ?>
            <ul class="mDet">
              <li style="width:30%">Class: {{$details->class_name}}</li>
              <li style="width:40%">Section: {{$details->current_section_code}}</li>
              <li style="width:20%">Roll no. {{$details->current_roll_number}}</li>
            </ul><br/>
          @endif
          for the invoices of {{$data->invoice_no}}<br/>
          in words rupees <?php $f = new NumberFormatter("en", NumberFormatter::SPELLOUT); ?>{{$f->format($data->paid_amount)}}
          <div class="footNote">
            <ul>
              <li style="width: 19%">Pre. Balance<div class="fig">{{$data->amount_to_be_paid}}</div></li>
              <li style="width: 19%">Amount Paid<div class="fig">{{$data->paid_amount}}</div></li>
              <li style="width: 19%">Balance<div class="fig"> {{$data->amount_to_be_paid - $data->paid_amount}} </div></li>
              <li style="width: 27%">Received by<div class="fig">{{$data->created_by}}</div></li>
            </ul>
          </div>
        </div><!-- sDetail ends -->
      </div><!-- cCopy ends -->
  @endfor

@else
  <h1>Invalid Receipt</h1>
@endif
</body>
</html>