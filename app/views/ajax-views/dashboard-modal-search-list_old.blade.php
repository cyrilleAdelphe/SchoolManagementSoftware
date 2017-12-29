
<div class="row">
  <div class="col-sm-12" style="max-height:250px; overflow:auto">
    
    @if($group == 'student')
    	@define $name = 'student_name'
    	<h4 class="text-red">Results for <span class="text-green">Student - {{HelperController::pluckFieldFromId('Classes', 'class_name', $class_id)}} {{HelperController::pluckFieldFromId('Section', 'section_code', $section_id)}}</span></h4>
    @elseif($group == 'guardian')
    	@define $name = 'guardian_name'
    	<h4 class="text-red">Results for <span class="text-green">Guardian - {{HelperController::pluckFieldFromId('Classes', 'class_name', $class_id)}} {{HelperController::pluckFieldFromId('Section', 'section_code', $section_id)}}</span></h4>
    @elseif($group == 'admin')
    	@define $name = 'employee_name'
    	<h4 class="text-red">Results for <span class="text-green">Staff</span></h4>
    @else
    	@define $name = 'name'
    	<h4 class="text-red">Results for <span class="text-green">SuperAdmin</span></h4>
    @endif
    <table id="pageList" class="table table-bordered table-striped">
      <thead>
        <tr>
          @if($checkbox == 'yes')
          <th><input type = "checkbox" id = "checkall"></th>
          @endif
          <th>SN</th>
          <th>Name</th>
          <th>ID</th>
          <th>Username</th>
        </tr>
      </thead>
      <tbody>
      	@if(count($data))
	      	@define $i = 1
	      	
	      	@foreach($data as $d)
	      		<tr>
              @if($checkbox == 'yes')
                <td><input type = "checkbox" class = "checkall"><input type = "hidden" name = "user_id[]" value = "{{$d->id}}"></td>
              @endif
	      			<td>{{$i++}}</td>
	      			<td>@if($group == 'guardian')
	      					{{$d->guardian_name}} ({{$d->student_name}}) 
	      				@elseif($group == 'student')
	      					{{$d->student_name}}
	      				@elseif($group == 'admin')
	      					{{$d->employee_name}}
	      				@else
	      					{{$d->name}}
	      				@endif
	      			</td>
	      			<td>{{$d->id}}</td>
              <td>{{$d->username}}</td>
              <td>
                <button type="button" class = "btn btn-default" data-dismiss="modal" onclick="findIdSelect('{{ $d->username }}', this)">
                  Select
                </button>
              </td>
	      		</tr>
			    @endforeach
          <input type = "hidden" id = "findIdGroup" value = "{{ $group }}" />
        @else
        	<tr>
        		<td>
        			<div class="alert alert-warning alert-dismissable">
                  <h4><i class="icon fa fa-warning"></i>No students in the class</h4>
              </div>
        		</td>
        	</tr>
        @endif
      </tbody>
    </table>
  </div>
</div><!-- row ends --> 