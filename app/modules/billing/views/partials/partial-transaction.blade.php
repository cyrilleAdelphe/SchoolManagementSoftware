<form method = "post" action = "{{URL::route('global-export-to-excel')}}" id = "export-to-excel-form">
<p id = "export-to-excel-file_name">{{str_replace(['/'], '-', $date_range)}}</p>
<a href = "#" class = "btn btn-success" id = "export-to-excel-button">Export To Excel</a>
<table class="table table-bordered table-striped myData" id = "transaction_table">
  <thead>
    <tr class = "export-to-excel-row">
      <th class = "export-to-excel-data">SN</th>
      <th class = "export-to-excel-data">Transaction date</th>
      <th class = "export-to-excel-data">Transaction no.</th>
      <th class = "export-to-excel-data">Received By</th>
      <th class = "export-to-excel-data" class = "export-to-excel-data">Transaction type</th>
      <th class = "export-to-excel-data">Invoice amount (CR)</th>
      <th class = "export-to-excel-data">Invoice amount (DR)</th>
    </tr>
    <tr class = "export-to-excel-row">
      <th class = "export-to-excel-data"></th>
      <th class = "export-to-excel-data"></th>
      <th class = "export-to-excel-data"></th>
      <th class = "export-to-excel-data"></th>
      <th class = "export-to-excel-data"><input type = "text" id = "transaction_type" value = "" placeholder="filter..."></th>
      <th class = "export-to-excel-data"><p style="color:red;">Total: </p><div id="result_cr"></div></th>
      <th class = "export-to-excel-data"><p style="color:red;">Total: </p><div id="result_dr"></div></th>
    </tr>
  </thead>
  <tbody>
    @define $i = 0
    @foreach($data as $d)
      <tr class = "export-to-excel-row">
        <td class = "export-to-excel-data">{{++$i}}</td>
        <td class = "export-to-excel-data">{{$d->transaction_date}}</td>
        <td class = "export-to-excel-data"><a href = "{{URL::route('show-invoice-from-transaction-number', $d->transaction_no)}}?apiToken={{ preg_replace( "/\r|\n/", "", Input::get('apiToken', base64_encode('0:0')) ) }}" data-lity>{{$d->transaction_no}}</a></td>
        <td class = "export-to-excel-data"> <?php 
        if(isset(json_decode(BillingInvoice::where('id', $d->related_invoice_id)->pluck('invoice_details'))->personal_details->name)) 
        { 
        	$name = json_decode(BillingInvoice::where('id', $d->related_invoice_id)->pluck('invoice_details'))->personal_details->name;
        } 
        else { $name = ''; } ?> {{ $name }}</td>
        <td class = "transaction_type_value export-to-excel-data">{{$d->transaction_type}}</td>
        @if(in_array($d->transaction_type, SsmConstants::$const_billing_types['credit']))
        <td class="invoice_cr export-to-excel-data">{{$d->transaction_amount}}</td>
        <td class = "export-to-excel-data"></td>
        @else
        <td class = "export-to-excel-data"></td>
        <td class="invoice_dr export-to-excel-data">{{$d->transaction_amount}}</td>
        @endif
      </tr>
      @endforeach
  </tbody>
</table>
</form>



