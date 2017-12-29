<link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
<link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">
<link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script>
     $(document).ready(function(){
        var sum = 0;

        function updateSum(){
          sum = 0;
          $('input:checked').each(function(){
          sum = sum + parseFloat($(this).val());
          });
          var totalIn$ = '$ ' + sum;
          $('#sumTotal').text(totalIn$);
        }
       
        $('input').change(function(){
          updateSum();
        });
        
        updateSum();
      })
    </script>
<div class="content"> 
      <div class="mTitle" style="margin-bottom: 15px">
        All income list 
      </div>
     
      <div class="row">
        <div class="col-sm-12">
          <table  class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>SN</th>
                <th>Income type</th>
                <th>Total balance</th>
                <th>Dues</th>
                <th>
                  Select
                </th>
              </tr>
            </thead>
            <tbody>
              <tbody>
              @define $total = 0
              @define $i=1
              @define $j=1
               @foreach($data['fee_titles'] as $fee_title => $amount)
               
                <tr>
                  <td>{{$i++}}</td>
                  <td>{{BillingHelperController::removeQuotesAndUnderScore($fee_title)}}</td>
                  <td class="sumNow">{{(float) $amount}}</td>
                  <td class="sumDue"></td>
                  <td>
                    <input type="checkbox" id="pay{{$j}}" class="css-checkbox" value="{{(float) $amount}}" checked/>
                    <label for="pay{{$j}}" class="css-label"></label>
                  </td>
                </tr>     
                @define $j++
                @define $total = $total + $amount
              @endforeach
            </tbody>
          </table>
          <form>
            <div class="budgetAmount">
            Credit Note Total: <span style="color: #000">$ {{$data['credit_note']['total']}}</span> <br>
            Total(without credit note): <span id="sumTotal" style="color: #000"><strong>0.00</strong></span><br>
            Grand Total: <span style="color: #000"> $ {{$total - $data['credit_note']['total']}}</span>
            </div>
            <form></form>
          <form method="POST" action=" {{ URL::route('add-income')}}">
          <input type="text" name="grand_total" value="{{$total - $data['credit_note']['total']}}">            
           <div class="pull-right"><input type="submit"  class="btn btn-success btn-flat btn-lg" name="submit"></div>
          </form>
        </div>
      </div>
    </div>
<script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>        