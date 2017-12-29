@if($res['status'] == 'error')

<div class="alert alert-warning alert-dismissable">
        <h4><i class="icon fa fa-warning"></i>{{$res['msg']}}</h4>
      
    </div>
@elseif(count($result['attendance_status']))
	                  <h1>{{HelperController::pluckFieldFromId('StudentRegistration', 'student_name', Input::get('student_id', 0))}}</h1>
<table id="fileList" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>SN</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                  @define $i = 1
                  @foreach($result['attendance_status'] as $date => $d)
                    <tr>
                      <td>{{$i++}}</td>
                      <td>{{$date}}</td>
                      <td>@if($d['status'] == 'Absent') <span class="text-red"> {{$d['status']}} </span> @else {{$d['status']}} @endif</td>
                      <td>{{$d['remarks']}}</td>
                    </tr>
                   @endforeach
                    
                  </tbody>
                </table>
@else
	<div class="alert alert-warning alert-dismissable">
        <h4><i class="icon fa fa-warning"></i>No Records Available</h4>
      
    </div>
@endif