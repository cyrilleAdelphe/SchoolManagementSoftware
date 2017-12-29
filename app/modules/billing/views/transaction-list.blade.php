@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Billing</h1>
@stop
@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
<link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('sms/assets/css/lity.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')

              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Choose date range</label>
                    <input type = 'text' name = 'daterange' value="Show current date" id = "myDate" class = 'form-control myDate required'>
                  </div>
                </div>
                <div class="col-sm-3">
                  <label style="color: #fff; display: block;">Generate</label>
                    
                    <input type="submit" class="btn btn-success btn-flat" id = "generate-transaction" value="Generate Transaction" />  
                  
                </div>
              </div><!-- row ends -->
              <div id = "ajax-content">
              </div>
              
              <br/>
@stop

@section('custom-js')
<!-- Billing-v1-changed-made-here -->
<script src = "{{asset('backend-js/export-to-excel.js')}}"></script>
<!-- Billing-v1-changed-made-here -->
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript" src="{{asset('sms/assets/js/lity.min.js')}}"></script>
<script type="text/javascript">
        $(function() {
                 
          $('.myDate').daterangepicker(
          {
              
              "showDropdowns": true,
              "showCustomRangeLabel": false,
              "alwaysShowCalendars": true,
              locale: {
                format: 'YYYY/MM/DD'
              },
              startDate: '{{date('Y/m/d')}}',
          }, 
          function(start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY/MM/DD') + ' to ' + end.format('YYYY/MM/DD') + ' (predefined range: ' + label + ')");
          });
        });

        $('#generate-transaction').click(function()
        {
          var date_range = $('#myDate').val();
        $('#ajax-content').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
          $.ajax({
            'url' : '{{URL::route('billing-api-get-transaction-list-view')}}',
            'method' : 'GET',
            'data' : {'date_range' : date_range}
          }).done(function(data)
          {
            $('#ajax-content').html(data);
            calculateSum_invoice_cr();
            calculateSum_invoice_dr();

          });
        });

        $(document).on('keyup', '#transaction_type', function()
        {
          var filter_text = $(this).val();
          var cr_sum = 0;
          var dr_sum = 0;

         $( ".transaction_type_value" ).each(function( index ) 
          {
              var currentRow = $(this).parent();

              currentRow.show();
               
              if(filter_text.length == 0 ) 
              {

                 if(isNaN(cr_sum))  
                  {
                    cr_sum = 0;
                  } 
                  if(isNaN(dr_sum))  
                  {
                    dr_sum = 0;
                  }
                  
                  var temp = parseFloat(currentRow.find('.invoice_cr').text()); 
		  if(isNaN(temp))
	          {
	          	temp = 0;
	          }
	          cr_sum += temp;
	          
	          var temp = parseFloat(currentRow.find('.invoice_dr').text());
	          if(isNaN(temp))
	          {
	          	temp = 0;
	          }
                  dr_sum += temp;

              }
              else 
              {
                if($(this).text().toLowerCase().indexOf(filter_text) == -1)
                {
                  currentRow.hide();
                }
                else
                {

                  if(isNaN(cr_sum))  
                  {
                    cr_sum = 0;
                  } 
                  if(isNaN(dr_sum))  
                  {
                    dr_sum = 0;
                  }
                  
                  var temp = parseFloat(currentRow.find('.invoice_cr').text()); 
		  if(isNaN(temp))
	          {
	          	temp = 0;
	          }
	          cr_sum += temp;
	          
	          var temp = parseFloat(currentRow.find('.invoice_dr').text());
	          if(isNaN(temp))
	          {
	          	temp = 0;
	          }
                  dr_sum += temp;

                }
             }

              
          });

         $('#result_cr').html(cr_sum.toFixed(2));
         $('#result_dr').html(dr_sum.toFixed(2));
        });

    

 function calculateSum_invoice_cr() {

    var sum = 0;

    $(".invoice_cr").each(function() 
    {
      var value = $(this).text();
      
      sum += parseFloat(value);
      
    });

$('#result_cr').html(sum.toFixed(2));
};

  function calculateSum_invoice_dr() {

    var sum = 0;
      $(".invoice_dr").each(function() {

         var value = $(this).text();

          sum += parseFloat(value);
      
      
    });
$('#result_dr').html(sum.toFixed(2));
};
   

</script>

@stop