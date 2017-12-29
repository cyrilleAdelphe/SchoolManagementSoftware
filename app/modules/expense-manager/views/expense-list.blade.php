@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{ asset('sms/assets/css/lity.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">

@stop

@section('page-header')    
  <h1>Expense Manager</h1>
@stop

@section('content')

         <div class="box">
            <div class="box-body">
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li @if(Input::old('active_tab') != 'tab_2') class="active" @endif><a href="#tab_1" data-toggle="tab">Expense list</a></li>
                  <li  @if(Input::old('active_tab') == 'tab_2') class="active" @endif><a href="#tab_2" data-toggle="tab">New expense</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane @if(Input::old('active_tab') != 'tab_2') active @endif" id="tab_1">
                    <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <label>Date filter</label>
                          <input class="form-control myDate" type="text" id="date" name="daterange" placeholder="choose date range" class = 'form-control myDate required'>
                        </div>
                      </div>
                    </div>
                    
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Title</th>
                          <th>Paid to</th>
                          <th>Paid from</th>
                          <th>Amount</th>
                          <th>Payment  Date</th>
                          <th>Payment type</th>
                          <th><input type="text" name="search" id="search" placeholder="Transaction ID"></th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      @if(count($expense_list))
                      <tbody id="pageList">
                      <?php
                      $i=1
                      ?>
                      @foreach($expense_list as $list)
                         <tr>
                          <td>{{ $i++}}</td>
                          <td>{{ $list->title}}</td>
                          <td>{{ $list->paid_to}}</td>
                          <td>{{ $list->account_name}}</td>
                          <td>Rs. {{ $list->amount}}</td>
                          <td>{{ date('j F Y ',strtotime($list->payment_date))}}</td>
                          <td>{{ $list->payment_type}}</td>
                          <td>{{ $list->transaction_id}}</td>
                          <td>
                            <a class="btn btn-flat btn-info btn-xs" type="button" href="{{ URL::route('expense-notes', $list->id)}}" data-lity><i class="fa fa-fw fa-info "></i></a>
                            <a class="btn btn-success btn-flat btn-xs" type="button" href="{{   route('expense-edit', $list->id)}}" data-lity><i class="fa fa-fw fa-edit "></i></a>
                            <a href="{{ URL::route('expense-delete', $list->id) }}" onclick = "confirmation(event)" class="btn btn-danger btn-flat btn-xs" ><i class="fa fa-fw fa-trash "></i></a>
                          </td>
                        </tr>
                        @endforeach
                        @else
                    <p style="color:red ; text-align:center; " >No records to show</p>
                    @endif
                      </tbody>
                    </table>
                    
                  </div><!-- tab 1 ends -->
                  <div class="tab-pane @if(Input::old('active_tab') == 'tab_2') active @endif" id="tab_2">
                    <div class="row">
                      <div class="col-sm-6">
                        <form method="POST" action="{{ URL::route('create-expense')}}" enctype='multipart/form-data'>
                        <input type="hidden" name="active_tab" value="tab_2">
                        
                           <div class="transferID">Transaction ID: {{ $transaction_id}}<input type="hidden" name="transaction_id" value="{{ $transaction_id }}"></div> 
                          <div class="form-group">
                            <label>Account name *</label>
                            <select class="form-control" name="account_id">
                              @foreach($account_type as $account=>$id)
                            <option value="{{ $account }}">{{ $id}}</option>
                              @endforeach
                            </select>
                          </div><div id="msg" style="color:red">{{ $errors->first('account_id')}}</div>
                          <div class="form-group">
                            <label>Title *</label>
                            <input type="text" name="title" class="form-control" value="{{ Input::old('title') }}">
                          </div><div id="msg" style="color:red">{{ $errors->first('title')}}</div>
                          <div class="form-group">
                            <label>Paid to *</label>
                            <input type="text" name="paid_to" class="form-control" value="{{ Input::old('paid_to') }}">
                          </div><div id="msg" style="color:red">{{ $errors->first('paid_to')}}</div>
                          <div class="form-group">
                            <label>Payment date</label>
                            <input type="" name="payment_date" class="form-control singleDate" placeholder="Insert date" value="{{ Input::old('payment_date')}}">
                          </div><div id="msg" style="color:red">{{ $errors->first('payment_date')}}</div>
                           <div class="form-group">
                            <label>Amount *</label>
                            <input type="text" name="amount" class="form-control " placeholder="Insert amount" value="{{ Input::old('amount')}}">
                          </div><div id="msg" style="color:red">{{ $errors->first('amount')}}</div>
                           <div class="form-group">
                            <label>Payment Type *</label>
                            <input type="text" name="payment_type" class="form-control " placeholder="Insert Payment type" value="{{ Input::old('payment_type')}}">
                          </div><div id="msg" style="color:red">{{ $errors->first('payment_type')}}</div>
                          <div class="form-group">
                            <label>Reference</label>
                            <input class="form-control" type="text" name="reference" placeholder="Insert reference"  value="{{ Input::old('reference') }}">
                            <small>Transaction id, check number etc</small>
                          </div>
                          <div class="form-group">
                            <label for="content">Notes</label>
                            <textarea class="textarea" placeholder="Insert your note here" name="notes" style="width: 100%; height: 70px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" > {{ Input::old('notes')}}</textarea>
                          </div> 
                          
                          <div class="form-group">
                            <label>Browse File</label>
                            <input class="form-control" type="file" name="pic" placeholder="Insert pic"  value="{{ Input::old('pic') }}"><br>
                           <div id="msg" style="color:red">{{ $errors->first('pic')}}</div>
                           <input class="form-control" type="text" name="pic_name" placeholder="Insert pic name "  value="{{ Input::old('pic_name') }}">
                          </div> 
                          <div class="form-group">
                          <button class="btn btn-success btn-flat btn-lg"> Submit</button>
                          </div>
                          {{Form::token()}}
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        

@stop

@section('custom-js')
<script type="text/javascript" src ="{{ asset('sms/assets/js/lity.min.js')}}"></script>
<script type="text/javascript" src ="{{ asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src ="{{ asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
         $(function() {
         
          $('#date').daterangepicker(
          {
              locale: {
                format: 'YYYY-MM-DD'
              }
          }, 
          function(start, end, label) {
    
var startDate= start._d;
var endDate= end._d;
//converts date format from thu dec 13 2016 (UTC) to YYYY-MM-DD
function convert(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth()+1)).slice(-2),
        day  = ("0" + date.getDate()).slice(-2);
    return [ date.getFullYear(), mnth, day ].join("-");
}

  $('#date').change(function()
{  
  $.ajax({  type : 'get',
        url  : '{{URL::route('search-date-expense')}}',
        data : {'start_date':convert(startDate),
            'end_date':convert(endDate)},

        success:function(data){

          $('#pageList').html(data);
        }
});
});

});
});
        
    </script>

    <script type="text/javascript">
      function confirmation(e) {
        var answer = confirm("Are you sure ?")
        if(!answer) {
          e.preventDefault();
          return false;
        }
      }
    </script>

    <script type="text/javascript">
      var keyupfunction = function() {
          var search_expense = $("#search").val();


            $.ajax({
                url: '{{ URL::route('search-expense')}}',
                data: {'searchExpense':search_expense},
                type: 'get',

                success:function(data)
                {
                  $('#pageList').html(data);
                }

            });
      }

      $(document).ready(function() {
        $('#search').keyup(keyupfunction);

      });
    </script>
    <script type="text/javascript">
      $(function() {
         
          $('.singleDate').daterangepicker(
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