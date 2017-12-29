<form method = "post" action = "{{URL::route('global-export-to-excel')}}" id = "export-to-excel-form">
<p id = "export-to-excel-file_name">{{str_replace(['/'], '-', $date_range)}}</p>
<a href = "#" class = "btn btn-success" id = "export-to-excel-button">Export To Excel</a>
<table class="table table-bordered table-striped myData" id = "transaction_table">
  <thead>
    <tr class = "export-to-excel-row">
      <th class = "export-to-excel-data">SN</th>
      <th class = "export-to-excel-data">Transaction date</th>
      <th class = "export-to-excel-data">Receipt no.</th>
      <th class = "export-to-excel-data">Received By</th>
      <th class = "export-to-excel-data">Paid By</th>
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
      <th class = "export-to-excel-data"></th>
      <th class = "export-to-excel-data"><p style="color:red;">Total: </p><div id="result_cr"></div></th>
      <th class = "export-to-excel-data"><p style="color:red;">Total: </p><div id="result_dr"></div></th>
    </tr>
  </thead>
  <tbody>
    @define $i = 0
    @foreach($data['data'] as $d)
      <tr class = "export-to-excel-row">
        <td class = "export-to-excel-data">{{++$i}}</td>
        <td class = "export-to-excel-data">{{$d->received_on}}</td>
        <td class = "export-to-excel-data">{{$d->receipt_no}}</td>
        <td class = "export-to-excel-data">{{ $d->created_by }}</td>
        <td class = "export-to-excel-data transaction_type_value">@if($d->received_from == 'student') {{ StudentRegistration::where('id', $d->received_id)->pluck('student_name') }} {{ StudentRegistration::where('id', $d->received_id)->pluck('last_name') }} @elseif($d->received_from == 'organization') {{ BillingDiscountOrganization::where('id', $d->received_id)->pluck('organization_name') }} @endif</td>
        <td class = "transaction_type_value export-to-excel-data"></td>
        
        <td class = "export-to-excel-data"></td>
        <td class="invoice_dr export-to-excel-data">{{$d->paid_amount}}</td>
        
      </tr>
      @endforeach
  </tbody>
</table>
</form>



