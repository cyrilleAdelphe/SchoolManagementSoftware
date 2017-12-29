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
            <input type="checkbox" name="student_id[{{$class_id}}][{{$section_id}}][]" value="{{$s->id}}" @if(in_array($s->id, $excluded_student_ids)) checked @endif class="minimal student_id" />
            <label for="std2" class="css-label">Not applicable</label>
          </td>
    </tr>
@endforeach
<tr><td><a href = "#" class = "btn btn-default add-edit-module-btn-save-details">Save</a></td></tr>
</tbody> 

@endif