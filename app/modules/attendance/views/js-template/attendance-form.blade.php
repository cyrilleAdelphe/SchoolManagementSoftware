@if(count($students)==0)
	No students in the class
@else
	<form method="post" action="{{URL::route("attendance-create-post")}}" id="formSave">
		<table id="catList" class="table table-bordered table-striped">
			<thead>
				<tr>
          <th>Roll No.</th>
          <th>Name</th>
        	<th>Status</th>
        </tr>
      </thead>
      <tbody>
      	@foreach($students as $student)
      		<tr>
      			<td>{{$student['current_roll_number']}}</td>
<td>{{$student['name']}} {{$student['last_name'] }}</td>
      			<td>
							<div class="row">
							  <div class="col-sm-12 col-md-5 col-xl-12">
							    <div class="row">
							      <div class="col-sm-12 col-md-4 col-xl-12">
							        <label>
							          <input type="radio" name="attendance{{$student['student_id']}}" value="a" class="flat-red" @if($student->attendance_status=='a') checked @endif 	/>

							        </label>
							        Absent
							      </div>
							      <div class="col-sm-12 col-md-4 col-xl-12">
							        <label>
							          <input type="radio" name="attendance{{$student['student_id']}}" value="l" class="flat-red" @if($student->attendance_status=='l') checked @endif/>                                  
							        </label>
							        Late 
							      </div>
							      <div class="col-sm-12 col-md-4 col-xl-12">
							        <label>
							          <input type="radio" value="p" name="attendance{{$student['student_id']}}" class="flat-red" @if($student->attendance_status=='p') checked @endif/>                                  
							        </label>
							        Present 
							      </div>
							    </div>
							  </div>
							  <div class="col-sm-12 col-md-7 col-xl-12">
							    <div class="form-group">
							      <input id="comment" name="comment{{$student['student_id']}}" class="form-control" type="text" placeholder="Enter comment" value={{$student->attendance_comment}}>
							    </div>
							  </div>
							</div>
						</td>
      		</tr>
      	@endforeach
      </tbody>
    </table>
    
    <input type="hidden" name="student_ids" value="{{$student_ids}}">
    <input type="hidden" name="class_id" value="{{$class_id}}">
    <input type="hidden" name="section_id" value="{{$section_id}}">
    <input type="hidden" name="date" id="attendanceFormDate">

    {{Form::token()}}
    {{-- <div class="form-group">
        <button id="attendanceFormSubmit" class="btn btn-primary" type="submit">Submit</button>
    </div> --}}
  </form>

  <div class='row'>
  	<a class = 'btn btn-app' href = '#' id = 'PraSave' @if(AccessController::checkPermission('attendance', 'can_create,can_edit') == false) disabled @endif>
  		<i class = 'fa fa-save'></i>Save
  	</a>

  	<a class = 'btn btn-app' href = '#'  id = 'PraSaveAndSendNotification' @if(AccessController::checkPermission('attendance', 'can_create,can_edit') == false) disabled @endif>
  		<i class='fa  fa-check-square-o'></i>Save & send notification
  	</a>
  </div>

  	
@endif
