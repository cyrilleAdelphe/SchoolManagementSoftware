 <table class="table table-bordered table-striped">
                
                <tbody>
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