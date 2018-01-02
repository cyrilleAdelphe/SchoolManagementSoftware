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
    <div class="content"> 
    @if(Session::has('success-msg'))
        <div class = "box-body">           
            <div class = "alert alert-success alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('success-msg'))}}">
                {{ Session::get('success-msg') }}
            </div>            
        </div>
        @endif
        @if(Session::has('error-msg'))
          <div class = "box-body">
            
            <div class = "alert alert-danger alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('error-msg'))}}">
                {{ Session::get('error-msg') }}
            </div>            
        </div>
        @endif


        <p id = "export-to-excel-file_name"></p>
        <div class = 'container row'>
            <form method = "post" action = "{{URL::route('global-export-to-excel')}}" id = "export-to-excel-form">
              <a href = "#" class = "btn btn-success" id = "export-to-excel-button">Export To Excel</a>
            </form>
        </div>
        <br>
        <div style = "display:none">

          <div class = "export-to-excel-row">
            <div class = "export-to-excel-data">SN</div>
            <div class = "export-to-excel-data">Name</div>
            {{-- <div class = "export-to-excel-data">Invoice Number</div> --}}
            <div class = "export-to-excel-data">Total</div>
          </div>
        </div>

      <div class="info-bar" style="margin-bottom: 15px">
        @if($related_user_group == 'organization')
        Due details of Organizations till {{$start_date}}
        @elseif($related_user_group == 'student')
        Due details of {{$related_user_group}} till {{$start_date}}
        @endif
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table  class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>SN</th>
                <th>Name</th>
                <th>Invoice Number</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>


               @define $i = 0
                @foreach($data as $d)
                <tr class = "export-to-excel-row">
                  <td class = "export-to-excel-data">{{++$i}}</td>
                  <td class = "export-to-excel-data">{{$d->name}}@if($d->related_user_group != 'organization') {{ $d->last_name }} @endif</td>
                  <td>
                    @define $invoice_numbers = explode(',', $d->invoice_number)
                    @define $financial_years = explode(',', $d->financial_year)
                    @foreach($invoice_numbers as $index => $invoice_number)
                      <a href = "{{URL::route('show-invoice-from-invoice-number', $invoice_numbers[$index])}}?financial_year={{$financial_years[$index]}}">{{$financial_years[$index]}}-{{$invoice_numbers[$index]}}</a>
                    @endforeach
                  </td>
                  <td class = "export-to-excel-data">{{$d->invoice_balance - $d->received_amount}}</td>
                  @if($d->related_user_group != 'organization')
                   <td><a href = " {{ URL::route('send-due-report-api', [$d->related_user_id , $d->invoice_balance , $d->received_amount]) }}" data-toggle="tootltip" title="Send Push Notification" class="btn btn-info btn-flat litty-dynamic"> <i class="fa fa-eye"></i></a> </td>
                   @endif
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
   
    <!-- jQuery 2.1.4 -->
    <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>

    <script src = "{{ asset('backend-js/export-to-excel.js') }}" type = "text/javascript"></script>

    <script>
      $(function()
      {
        $('#export-to-excel-file_name').html($('.info-bar').text().trim());
      })
    </script>

  </body>
</html>