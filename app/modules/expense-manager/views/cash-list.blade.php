@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{ asset('sms/assets/css/lity.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">

@stop

@section('page-header')    
  <h1>Cash in hand - <span style="color: #ff2c02">{{ $final_cash_in_hand}}</span></h1>
@stop

@section('content')
    <div class="box">
            <div class="box-body">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Date filter</label>
                    <input class="form-control myDate" type="text" name="daterange" id="date" placeholder="choose date range">
                  </div>
                </div>
                <div class="col-sm-8">
                  <a href="{{ URL::route('transfer') }}" class=" pull-right btn btn-primary btn-flat" style="margin-bottom:15px" data-lity><i class="fa fa-fw fa-plus"></i> Add amount</a>
                </div>
              </div>
              <table  class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>SN</th>
                    <th>Amount</th>
                    <th>Transfer date</th>
                    <th>Transferred from</th>
                    <th><input type="text" name="search" id ="search" placeholder="Transaction ID"></th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="pageList">
                @if(count($cash_list))
                @define $i=1;
                @foreach($cash_list as $list)
                  <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $list->amount}}</td>
                    <td>{{ date('F j Y',strtotime($list->date))}}</td>
                    <td>{{ $list->account_name}}</td>
                    <td>{{ $list->transaction_id}}</td>
                    <td>
                      <a class="btn btn-info btn-flat btn-xs" type="button" href="{{ URL::route('transfer-info',$list->id)}}" data-lity><i class="fa fa-fw fa-info "></i></a>
                      <a class="btn btn-success btn-flat btn-xs" type="button" href="{{ URL::route('transfer-edit', $list->id)}}" data-lity><i class="fa fa-fw fa-edit "></i></a>
                      <a href="{{ URL::route('cash-delete', $list->id)}}" onclick = "confirmation(event)" class="btn btn-danger btn-flat btn-xs"><i class="fa fa-fw fa-trash "></i></a>
                    </td>
                  </tr>
                  @endforeach
                  @else
                  <p style="color:red; text-align:center;">No records to show
                  @endif
                 </tbody>
               </table>
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
//conerts date format from thu dec 13 2016 (UTC) to YYYY-MM-DD
function convert(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth()+1)).slice(-2),
        day  = ("0" + date.getDate()).slice(-2);
    return [ date.getFullYear(), mnth, day ].join("-");
}

  $('#date').change(function()
{  
  $.ajax({  type : 'get',
        url  : '{{URL::route('search-date-cash')}}',
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
          var search_cash = $("#search").val();


            $.ajax({
                url: '{{ URL::route('search-cash')}}',
                data: {'searchCash':search_cash},
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
      
      setTimeout(function() {
        $('.alert-success').fadeOut('slow');
        }, 2000);
     </script>
    

@stop

@stop