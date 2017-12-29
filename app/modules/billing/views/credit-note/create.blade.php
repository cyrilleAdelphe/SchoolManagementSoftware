<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Create Fee | Eton</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
    <!-- FontAwesome 4.3.0 -->
    <link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />    
    <!-- Theme style -->
    <link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
    
  </head>
  <body>

    <div class="content"> 
    @if(Session::has('error-msg'))
          <div class = "box-body">
            
            <div class = "alert alert-danger alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('error-msg'))}}">
                {{ Session::get('error-msg') }}
            </div>            
        </div>
        @endif
        @if(Session::has('success-msg'))
        <div class = "box-body">           
            <div class = "alert alert-success alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('success-msg'))}}">
                {{ Session::get('success-msg') }}
            </div>            
        </div>
        @endif
        @if(Session::has('warning-msg'))
        <div class = "box-body">           
            <div class = "alert alert-info alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('info-msg'))}}">
                {{ Session::get('warning-msg') }}
            </div>           
        </div>
         @endif
    @if($data)
    <form action = "{{URL::route('billing-credit-note-post', $invoice_id)}}" method = "post">
        @define $prev_credit_notes = BillingInvoice::where('related_invoice_id', $invoice_id)->where('invoice_type', 'credit_note')->get();

        <table class = "table table-bordered table-striped">
          <thead>
            <tr>
              <th>Credit Note Invoice Number</th>
              <th>Credit Note Amount</th>
              <th>Description</th>
            </tr>
          </thead>
          @define $prev_invoice_balance = 0
        @foreach($prev_credit_notes as $p)
          <tr>
            <td>{{$p->financial_year}}-{{$p->invoice_number}}</td>
            <td>{{$p->received_amount}}</td>
            <td>{{$p->note}}</td>
            @define $prev_invoice_balance += $p->received_amount
          </tr>
        @endforeach
        <tr>
          <th>Total</th>
          {{-- //// billing code added here --}}
          <th colspan="2">{{$prev_invoice_balance}}</th>
          {{-- //// billing code added here --}}
        </tr>
        <input type = "hidden" name = "previous_credit_note_balance" value = "{{$prev_invoice_balance}}">
        </table>
        @define $data_details = json_decode($data->invoice_details, true)
      <div class="row"> 
        <div class="col-sm-5">
          <table  class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Invoice Number</th>
                <th>Total Invoice Amount</th>
                <th>Received Amount (in cash)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{$data_details['personal_details']['name']}}</td>
                <td>{{ $data->financial_year }} - {{$data->invoice_number}}</td>
                <td>{{ $data->invoice_balance }}<input type = "hidden" name = "invoice_total" value = "{{ $data->invoice_balance }}"></td>
                <td>{{ $data->received_amount }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      


  <div class = "container">
    <div class = "row">
        
      <table class = "table table-bordered table-striped">
        <tr>
          <th>Name: </th>
          <th>{{$data_details['personal_details']['name']}}</th>
        </tr>
        @if($data_details['personal_details']['group'] == 'student')
        <tr>
          <th>Roll: </th>
          <th>{{$data_details['personal_details']['roll_number']}}</th>
        </tr>
        @endif
        <tr>
          <th>Class Section : </th>
          <th>
            {{$data_details['personal_details']['class']}} - {{$data_details['personal_details']['section']}}
            <input type = "hidden" name = "class" value = "{{$data_details['personal_details']['class']}}">
            <input type = "hidden" name = "section" value = "{{$data_details['personal_details']['section']}}">
          </th>
        </tr>
        @foreach($data_details['fees'] as $fee)
          <tr>
            <th>Title: </th>
            <th>{{$fee['fee_title']}}</th>
          </tr>
          <tr>
            <th>Amount: </th>
            <th>{{$fee['fee_amount']}}</th>
          </tr>
          @if($data_details['personal_details']['group'] == 'organization')
            <tr>
              <th>Recipient: </th>
              <th>{{$fee['recipient']}}</th>
            </tr>
          @endif
        @endforeach

        @if($data_details['personal_details']['group'] == 'student')
          @if(isset($data_details['discount']))
            @foreach($data_details['discount'] as $d)
              <tr>
                <th>Discount By: </th>
                <th>{{$d['organization_name']}}</th>
              </tr>
              <tr>
                <th>Discount title: </th>
                <th>{{$d['discount_title']}}</th>
              </tr>
              <tr>
                <th>Discount on: </th>
                <th>{{$d['fee_title']}}</th>
              </tr>
              <tr>
                <th>Discount Amount: </th>
                <th>{{$d['discount_amount']}}</th>
              </tr>
            @endforeach
          @endif
        @endif

        <tr>  
          <th>Taxable: </th>
          <th>{{$data_details['summary']['taxable_amount']}}</th>
        </tr>
        <tr>
          <th>Untaxable: </th>
          <th>{{$data_details['summary']['untaxable_amount']}}</th>
        </tr>
        <tr>
          <th>Sum Without Tax: </th>
          <th>{{$data_details['summary']['sum_without_tax']}}</th>
        </tr>
        <tr>
          <th>Tax: </th>
          <th>{{$data_details['summary']['tax']}}</th>
        </tr>
        <tr>
          <th>Total: </th>
          <th>{{$data_details['summary']['total']}}</th>
        </tr>
        <tr>
          <th>Paid Amount: </th>
          <th>{{$data->received_amount}}</th>
        </tr>
      </table>
    </div>
  </div>

      <div class = "row">
        
        <table  class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="col-sm-2">Invoice Number</th>
              <th class="col-sm-2">Amount</th>
              <th>Description</th>
              <th>Tax Included (if yes tax will be calculated automatically for entered amount)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{$invoice_number}}<input type = "hidden" name="number" class="form-control" value = "{{$invoice_number}}"/></td>
              <td><input name="amount" type = "number" step=0.01 max = {{$data_details['summary']['total']}} class="form-control" /></td>
              <td><input name="description" class="form-control" required/></td>
              <td><input type="radio" name = "tax_included"  value = "yes"/>Yes<input type="radio" name = "tax_included"  value = "no" checked/>No</td>
            </tr>
          </tbody>
        </table>
      </div>
      <br/>
          
    {{Form::token()}}
    <input type = "submit" class="btn btn-lg btn-flat btn-success" value = "Save">
    </form>
    @else
      <h1>Invoice not found</h1>
    @endif
    </div>
          
    <!-- jQuery 2.1.4 -->
    <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>

    <script>
    </script>

  </body>
</html>