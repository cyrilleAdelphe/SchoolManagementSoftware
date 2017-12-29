@extends('backend.'.$current_user->role.'.submain')
@section('content')
<div class="content">
      <div class="mTitle" style="margin-bottom: 15px">
        Transfer note of Rs. {{ $transfer_info->amount }} made at {{ date('j F Y ',strtotime($transfer_info->date)) }} | Transfer ID: {{ $transfer_info->transaction_id}}
      </div>
      @if($transfer_info->reference)
       <div class="transferID">Reference: {{ $transfer_info->reference }}</div> 
       @else
       No reference found
       @endif
       <br><br>
       @if($transfer_info->notes)
        <p>
        {{ $transfer_info->notes }}
      </p>
      @else
      No notes found
      @endif
    </div>
@stop
@stop