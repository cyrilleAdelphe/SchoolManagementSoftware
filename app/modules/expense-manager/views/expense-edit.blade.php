@extends('backend.'.$current_user->role.'.submain')
@section('content')
<div class="content"> 
                      <form action="{{ URL::route('edit-expense', $expense->id) }}" method="POST" target="_top" enctype='multipart/form-data'>
                          <div class="form-group">
                            <label>Account name *</label>
                            
                             <select name="account_id" id="account_id" class="form-control">
                             @foreach ($account_type as $account => $p)
                               <option value='{{$account}}' @if($account == $expense->account_id) selected @endif>{{$p}}</option> 
                              @endforeach
                             </select>
                          </div>
                           <div class="form-group">
                            <label>Title *</label>
                            <input type="text" name="title" class="form-control" value="{{ $expense->title}}" required>
                          </div><div id="msg" style="color:red">{{ $errors->first('title')}}</div>
                          <div class="form-group">
                            <label>Paid to *</label>
                            <input type="text" name="paid_to" value="{{ $expense->paid_to}}" class="form-control" required>
                          </div><div id="msg" style="color:red">{{ $errors->first('paid_to')}}</div>
                          <div class="form-group">
                            <label>Payment date</label>
                            <input type="" name="payment_date" value = "{{ $expense->payment_date}}" class="form-control myDate" placeholder="current date" required>
                          </div><div id="msg" style="color:red">{{ $errors->first('payment_date')}}</div>
                          <div class="form-group">
                            <label>Amount *</label>
                            <input type="text" name="amount" class="form-control " placeholder="Insert amount" value="{{ $expense->amount}}" required>
                          </div><div id="msg" style="color:red">{{ $errors->first('amount')}}</div>
                           <div class="form-group">
                            <label>Payment Type </label>
                            <input type="text" name="payment_type" class="form-control " placeholder="Insert Payment type" value="{{ $expense->payment_type}}" required>
                          </div><div id="msg" style="color:red">{{ $errors->first('payment_type')}}</div>
                          <div class="form-group">
                            <label>Reference</label>
                            <input class="form-control" type="text" name="reference" value="{{ $expense->reference}}" >
                            <small>Transaction id, check number etc</small>
                          </div>   
                          <div class="form-group">
                            <label for="content">Notes</label>
                            <textarea class="textarea" placeholder="Insert your note here" name= "notes" style="width: 100%; height: 70px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ $expense->notes}}</textarea>
                          </div>   
                           <div class="form-group">
                            <label>Browse File</label>
                            <input class="form-control" type="file" name="pic" placeholder="Insert pic" > <img src="{{ URL::to('public/expense-photos', $expense->pic)}}"  width="80" height="80" onerror="this.src= '{{ asset('/expense-photos/default.png')}}';"> 
                           <input class="form-control" type="text" name="pic_name" placeholder="Insert pic name "  value="{{ $expense->pic_name }}">
                          </div> 
                          <div class="form-group">
                          <button class="btn btn-success btn-flat btn-lg" >Update</button>
                          </div>
                          {{ Form::token()}}
                        </form>
</div>
@stop
@section('custom-js')
<script type="text/javascript" src ="{{ asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src ="{{ asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
       $(function() {
         
          $('.myDate').daterangepicker(
          {
              "singleDatePicker": true,
              locale: {
                format: 'YYYY-MM-DD'
              },
              

          }, 
          function(start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
          });
        });
</script>
    <script type="text/javascript">
      
      setTimeout(function() {
        $('.alert-success').fadeOut('slow');
        }, 2000);
     </script>
    
@stop
@stop
