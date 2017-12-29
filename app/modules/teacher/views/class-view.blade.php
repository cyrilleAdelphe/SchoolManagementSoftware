@if($classes)
<div class="row">
  <div class="col-sm-12">
      <table id="pageList" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>SN</th>
            <th>Class</th>
            <th>Section</th>
            <th>Is Class Teacher</th>
          </tr>
        </thead>
        <tbody>
        	@define $i=1
        	@foreach($classes as $class => $value)
        	<tr>
            <td>{{$i++}}</td>
            <td>{{ $value->class_name}}</td>
            <td>{{ $value->section_code}}</td>
            <td>{{ $value->is_class_teacher}}</td>
            
          </tr>
          @endforeach
          
        </tbody>
      </table>
  </div>
</div><!-- row ends -->
@else
<p style="color:red;" align="center">Sorry you are not assigned as teacher in any of the classes</p>
@endif