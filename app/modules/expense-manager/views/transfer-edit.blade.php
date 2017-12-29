@extends('backend.'.$current_user->role.'.submain')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/daterangepicker/daterangepicker.css')}}">
@stop

@section('content')

 <div class="content" style="padding-left: 30px">
       <form action="{{ URL::route('transfer-edit',$cash->id)}}" method="POST" target="_top">
        <div class="row"> 
          <div class="col-sm-6">       
            <div class="form-group">
              <label>From Account *</label>
              <select class="form-control" name="account_id">
                @foreach($account_type as $index=>$account)
                <option value="{{ $index}}" @if($index == $cash->account_id) selected @endif>{{ $account }}</option>
                @endforeach
              </select>
            </div>  
            <div class="form-group">
              <label>Date *</label>
              <input class="form-control myDate" type="text" name="date" placeholder="Today's date" value="{{ $cash->date}}" required>
            </div><div id="msg" style="color:red">{{ $errors->first('date')}}</div>   
            <div class="form-group">
              <label>Amount *</label>
              <input class="form-control" type="text" name="amount" value="{{ $cash->amount}}"  required>
            </div><div id="msg" style="color:red">{{ $errors->first('amount')}}</div>
            <div class="form-group">
              <label>Reference</label>
              <input class="form-control" type="" name="reference" value="{{ $cash->reference}}" >
              <small>Transaction id, check number etc</small>
            </div>   
            <div class="form-group">
              <label for="content">Notes</label>
              <textarea class="textarea" placeholder="Insert your note here" name= "notes" style="width: 100%; height: 70px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ $cash->notes}}</textarea>
            </div>   
            <div class="form-group">
              <button class="btn btn-success btn-flat btn-lg">Transfer</button>
            </div>  
          </div>
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