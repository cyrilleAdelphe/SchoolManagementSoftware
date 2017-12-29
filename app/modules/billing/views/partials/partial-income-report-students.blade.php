@extends('backend.'.$role.'.main')
@section('page-header')
  <h1 id = "export-to-excel-file_name">Income Report Student </h1>
@stop
@section('content')
<form method = "post" action = "{{URL::route('global-export-to-excel')}}" id = "export-to-excel-form">
  <a href = "#" class = "btn btn-success" id = "export-to-excel-button">Export To Excel</a>
<br>
<table  class="table table-bordered table-striped myData">
  <tbody>
    
    <tr class = "export-to-excel-row">
      <th class = "export-to-excel-data">Students</th>
      <th class = "export-to-excel-data">Total Amount</th>
      @foreach($fee_titles as $f)
      @define $f = BillingHelperController::removeQuotesAndUnderScore($f)
      <th class = "export-to-excel-data">{{BillingHelperController::removeQuotesAndUnderScore($f)}}</th>
      @endforeach
      <th class = "export-to-excel-data">Credit Note</th>
      <th class = "export-to-excel-data">Credit Note Tax</th>
      <th class = "export-to-excel-data">Flat Discounts</th>
      <th class = "export-to-excel-data">Received Amount</th>
      <th class = "export-to-excel-data">Unpaid Amount</th>
    </tr>
    @foreach($data['data'] as $groups => $temp)
      @foreach($temp as $related_user_id => $details)
      <tr class = "export-to-excel-row">
        <td class = "export-to-excel-data">{{$data['student_names'][$groups][$related_user_id]['name']}}</td>
        <td class = "export-to-excel-data">{{$data['total_amount'][$groups][$related_user_id]}}</td>
        
        @foreach($fee_titles as $f)
           
                  @if(isset($details['fees'][$f]))
                    <td class = "export-to-excel-data">{{(float) $details['fees'][$f]}}</td>
                  @else
                    <td class = "export-to-excel-data"> 0 </td>
                  @endif
          
        @endforeach
        
        
        @define $credit_note = isset($data['credit_note'][$groups][$related_user_id]['total']) ? $data['credit_note'][$groups][$related_user_id]['total'] : 0
        <th class = "export-to-excel-data">{{$credit_note}}</th>

        @define $credit_note_tax = isset($data['credit_note'][$groups][$related_user_id]['tax']) ? $data['credit_note'][$groups][$related_user_id]['tax'] : 0
        <th class = "export-to-excel-data">{{$credit_note_tax}}</th>

        <th class = "export-to-excel-data">{{$data['flat_discounts'][$groups][$related_user_id]}}</th>
        <td class = "export-to-excel-data">{{ $data['received_amount'][$groups][$related_user_id]}}</td>
        <td class = "export-to-excel-data">{{ $data['unpaid_amount'][$groups][$related_user_id]}}</td>
      </tr>
      @endforeach
    @endforeach

  </tbody>
</table>
</form>
@stop

@section('custom-js')
  <script src = "{{asset('backend-js/export-to-excel.js')}}"></script>
@stop

