<div class="info-bar"><strong>Subject List of</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Session: {{AcademicSession::where('id', $session_id)->pluck('session_name')}} | Class: {{Classes::where('id', $class_id)->pluck('class_name')}} | Section: {{Section::where('id', $section_id)->pluck('section_code')}} </div>
@if(count($data))
<table  class="table table-bordered table-striped ">
  <thead>
    <tr>
      <th>SN</th>
      <th>Subject</th>
    </tr>
  </thead>
  <tbody>
    @define $i = 0
    @foreach($data as $d)
    <tr>
      <td>{{++$i}}</td>
      <td>{{$d->subject_name}}</td>
      <td>
        <a data-toggle="tootltip" title="View Detail" class="btn btn-info btn-flat litty-dynamic" data-lity href="{{URL::route('teacher-cas-sub-topics-create-edit', [$d->id])}}">Manage Sub Topics <i class="fa fa-eye"></i>
        </a>
        <a data-toggle="tootltip" title="View Detail" class="btn btn-danger btn-flat" href="{{URL::route('teacher-cas-subtopic-assign-get', [$d->id])}}">Assign Marks <i class="fa fa-eye"></i>
        </a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@else

  No Subjects Found

@endif
<!-- ends -->