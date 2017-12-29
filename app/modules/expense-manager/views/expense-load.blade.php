<table class="table table-bordered table-striped">
                      
                      @if(count($expense_list))
                      <tbody id="pageList">
                      <?php
                      $i=1
                      ?>
                      @foreach($expense_list as $list)
                         <tr>
                          <td>{{ $i++}}</td>
                          <td>{{ $list->title }}</td>
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
@section('custom-js')
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
    @stop
