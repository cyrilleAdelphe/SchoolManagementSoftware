                @define $total = 0
                <div class="info-bar" style="margin-bottom: 15px">
                  Due Reports | Date till {{$start_date}}
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <table  class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Group</th>
                          <th>Class - Section</th>
                          <th>Total</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @define $i = 0
                        @foreach($data as $related_user_group => $dat)
                          @foreach($dat as $class_section => $d)
                          <tr>
                            <td>{{++$i}}</td>
                            <td>{{$related_user_group}}</td>
                            <td>{{$class_section}}</td>
                            <td>{{$d}}</td>
                            @define $total += $d
                            <td><a data-toggle="tootltip" title="View Detail" class="btn btn-info btn-flat litty-dynamic" href="{{URL::route('billing-api-remaining-due-details-list')}}?related_user_group={{$related_user_group}}&class_section={{$class_section}}"> <i class="fa fa-eye"></i></a> </td>
                          </tr>
                          @endforeach
                        @endforeach
                        <tr>
                          <td colspan="3" style="text-align: center"><strong>Total</strong></td>
                          <td colspan="2"><strong>{{$total}}</strong></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>