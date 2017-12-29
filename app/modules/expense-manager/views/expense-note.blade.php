@extends('backend.'.$current_user->role.'.submain')

@section('content')
<div class="content">
      <div class="mTitle" style="margin-bottom: 15px">
        Expense note of Rs. {{ $expense_detail->amount }} made at {{ date('F  j Y ',strtotime($expense_detail->payment_date))}} | Transaction ID: {{ $expense_detail->transaction_id}}
      </div>
      @if($expense_detail->reference)
       <div class="transferID">Reference: {{ $expense_detail->reference}}</div> 
       @else
       No reference found
       @endif
       <br><br>
       @if($expense_detail->notes)
        Notes: {{ $expense_detail->notes }}
        @else 
        No notes found
        @endif
        <br><br>
      Pic Name: {{ $expense_detail->pic_name }}
      <br>
      
      <img src="{{ URL::to('public/expense-photos', $expense_detail->pic)}}" width="240" height="240" onerror="this.src= '{{ asset('/expense-photos/default.png')}}';"> 
       
</div>
@stop
@stop

