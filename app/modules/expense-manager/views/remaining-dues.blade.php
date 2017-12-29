<link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
<link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">
<link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('sms/assets/css/custom.css')}}" rel="stylesheet" type="text/css" />

                @define $total = 0
                <div class="info-bar" style="margin-bottom: 15px">
                  Due Reports |
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