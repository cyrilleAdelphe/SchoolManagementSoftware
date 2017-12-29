@if(count($dues))

  <table  class="table table-bordered table-striped">
    <thead>
      <tr>
        <th></th>
        <th>Invoice number</th>
        <th>Year & Month</th>
        <th>Invoice Balance</th>
        <th>Received Amount</th>
        {{-- ////billing code added here --}}
        <th>Credit Notes</th>
        {{-- ////billing code added here --}}
        <th>Remaining Balance</th>
      </tr>
    </thead>

    <tbody>
    <?php $i = 1; 
      $total_remaining = 0;
    ?>
    @foreach($dues as $d) 
    <input type = "hidden" name = "financial_year[]" value = "{{$d->financial_year}}">
        <input type = "hidden" name = "invoice_number[]" value = "{{$d->invoice_number}}">    
        <tr>
          <td>
           
            <input id="inv{{$i}}"  type = "checkbox" name = "invoice_id[]" value = "{{$d->id}}" checked>
            <label for="inv{{$i++}}" class="css-label"></label>
          </td>
          <td>
            <a href = "{{URL::route('show-invoice-from-invoice-number', [$d->invoice_number])}}?financial_year={{$d->financial_year}}">{{$d->financial_year}}-{{$d->invoice_number}}</a>
            <a href = "{{URL::route('billing-credit-note-get', $d->id)}}" target="_blank">Make a Credit Note</a>
          </td>
          <td>{{$d->year_in_ad}} {{$d->month_in_ad}} ({{$d->year_in_bs}} {{$d->month_in_bs}})</td>
          <td>{{$d->invoice_balance}}</td>
          <td>{{$d->received_amount}}</td>
          {{-- ////billing code added here --}}
          <?php
            if(isset($credit_note_data[$d->id]))
            {
              $credit_note = $credit_note_data[$d->id];
            }
            else
            {
              $credit_note = 0;
            }
          ?>
          <td>{{ $credit_note }}</td>
          <td><?php $a = $d->invoice_balance - (float) $d->received_amount - $credit_note; echo (float)$a; ?></td>
          {{-- ////billing code added here --}}
        </tr>  
        <?php $total_remaining = $total_remaining + $a;?>  
        <thead>
        @endforeach
        <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>Total</th>
        <th><?php echo (float)$total_remaining;?></th>
        </tr>
        </thead>
    @else
      @if($student_id)
        <div class="happy-info">No Remaining Dues</div>
    @endif
  </tbody>
 </table>
@endif