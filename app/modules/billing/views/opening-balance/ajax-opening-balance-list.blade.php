              <div class="mTitle" style="margin-bottom: 15px">
                Opening balance of  |  Date: {{$date->format('d M Y')}}  |  Class: {{Classes::where('id', $class_id)->pluck('class_name')}}  |  Section: {{$section_code}}
              </div>
              <div class="row">
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <table  class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>SN</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Balance</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                      @if(count($data))
                        @define $i= 0
                        @foreach($data as $s)
                        <tr>
                          <td>{{$i}}</td>
                          <td>{{$s->username}} <input type = "hidden" value = "{{$s->id}}" name = "related_user_id[]"</td>
                          <td>{{$s->student_name}} {{ $s->last_name}}</td>
                          <td><input type = "text" step=0.01 class="myInput opening_balance" value="@if(Input::old('opening_balance'.'.'.$i)) {{Input::old('opening_balance'.'.'.$i)}} @else {{$s->opening_balance}} @endif" name = "opening_balance[]"/></td>
                        </tr>
                        @define $i = $i+1
                        @endforeach
                      @else
                        No students Found
                      @endif
                    </tbody>
                  </table>
                </div>
              </div><!-- row ends -->