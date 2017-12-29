
    <div class = "row">
      <div class="col-sm-2">
      <label>Session</label>
      @define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();

      <select class = "academic_session_id form-control" name = "academic_session_id[]">
      @foreach($sessions as $s)
        @if($selected_session_id)
          <option value = "{{$s->id}}" @if($s->id == $selected_session_id) selected @endif>{{$s->session_name}}</option>
        @else
          <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
        @endif
      @endforeach
      </select>
      
    </div>
    <div class="col-sm-2">
      <label>Class</label>
      {{$class_html}}
    </div>
    <div class="col-sm-2">
      <label>Section</label>
      {{$section_html}}
    </div>
    
    <div class="col-sm-2">
      <label>Amount</label>
      <input class="form-control amount" type="number" name = "amount[]" placeholder="Enter amount" value = "{{$amount}}">
    </div>
  </div>

  <div class = "row">
    <table class="pageList table table-bordered table-striped">
    @if($count == 0)

      <h1>{{$message}}</h1>

    @else
    <thead>
      <tr>
        <th>Roll no.</th>
        <th>ID</th>
        <th>Name</th>
        <th>
          <input type="checkbox"  id="checkboxG1" class="minimal" />
          <label for="checkboxG1" class="css-label">Not Applicable To</label>
        </th>
      </tr>

    </thead>
    <tbody>
    @foreach($students as $s)
        <tr>
          <td class = "roll_number">{{$s->current_roll_number}}</td>
          <td class = "username">{{$s->username}}</td>
          <td class = "student_name">{{$s->student_name}}</td>
          <td>
            <input type="checkbox" name="student_id[{{$selected_class_id}}][{{$selected_section_id}}][]" value="{{$s->id}}" @if(in_array($s->id, $excluded_student_ids)) checked @endif class="minimal student_id" />
            <label for="std2" class="css-label">Not applicable</label>
          </td>
        </tr>
    @endforeach
      <tr><td><a href = "#" class = "btn btn-default add-edit-module-btn-save-details">Save</a></td></tr>
    </tbody> 

    @endif
  </table>
  </div>
  
