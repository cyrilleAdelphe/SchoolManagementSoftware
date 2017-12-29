<h4><span class="text-green">{{$start_date->format('Y-m-d')}}</span> To <span class="text-green">{{$end_date->format('Y-m-d')}}</span></h4>
                        @if(count($data))
                        <table id="fileList" class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Roll No.</th>
                              <th>Name</th>
                              <th>Present days</th>
                              <th>Absent days</th> 
                              <th>Late days</th>                              
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data as $student_id => $d)
                            <tr>
                              
                              <td>{{$d['roll']}}</td>
                              		 <td>{{$d['name']}} {{$d['last_name']}} </td>
                              <td>{{$d['present_days']}}</td>
                              <td>{{$d['absent_days']}}</td>
                              <td>{{$d['late_days']}}</td>
                              <td>
                                <a href="{{URL::route('attendance-view-student-get')}}?student_id={{$d['student_id']}}&date_range={{$date_range}}&class_id={{$class_id}}&section_code={{$section_code}}" data-toggle="tooltip" title="View Detail" class="btn btn-info btn-flat">
                                  <i class="fa fa-fw fa-eye"></i>
                                </a>
                              </td>
                            </tr>
                            @endforeach
                          </tbody>
                        </table>
                        @else
                        <div class="alert alert-warning alert-dismissable">
        <h4><i class="icon fa fa-warning"></i>No Records Available</h4>
      
    </div>
                        @endif