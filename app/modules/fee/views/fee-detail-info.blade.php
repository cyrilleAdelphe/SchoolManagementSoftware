<div class="row">
  <div class="col-sm-12">
      <table id="pageList" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>SN</th>
            <th>Class</th>
            <th>Section</th>
            <th>Total Students</th>
            <th>Paid</th>
            <th>Unpaid</th>
            <th>Generated at</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        	@define $i=1
        	@foreach($details as $detail)
        	<tr>
            <td>{{$i++}}</td>
            <td>{{$detail->class}}</td>
            <td>{{$detail->section}}</td>
            <td>{{$detail->total_students}}</td>
            <td>{{$detail->paid_students}}</td>
            <td>{{$detail->total_students - $detail->paid_students}}</td>
            <td>{{$detail->generated_at}}</td>
            <td>
              <div class="row">
                <div class="col-sm-3">
                  <a href="{{URL::route('fee-class-get').'?academic_session_id='.$academic_session_id.'&class_id='.$detail->class_id.'&section_id='.$detail->section_id.'&month='.$month}}" data-toggle="tooltip" title="View detail" class="btn btn-info btn-flat">
                    <i class="fa fa-fw fa-eye"></i>
                  </a>
                </div>

                <div class="col-sm-3">
                  <form action="{{URL::route('fee-generate-post')}}" method="post">
                    <input type="hidden" name="academic_session_id" value="{{$academic_session_id}}">
                    <input type="hidden" name="class_id" value="{{$detail->class_id}}">
                    <input type="hidden" name="section_id" value="{{$detail->section_id}}">
                    <input type="hidden" name="month" value="{{$month}}">
                    <input type="hidden" name="is_active" value="yes" />
                                  
                    <button data-toggle="tooltip" title="Generate" type="submit" class="btn btn-flat btn-success">
                      <i class="fa fa-fw fa-bar-chart"></i>
                    </button>
                    {{ Form::token() }}
                  </form>
                </div>
              </div>

              {{-- <a href="{{URL::route('fee-generate-get').'?academic_session_id='.$academic_session_id.'&class_id='.$detail->class_id.'&section_id='.$detail->section_id.'&month='.$month}}" data-toggle="tooltip" title="Generate Fee" class="btn btn-flat btn-success">
                <i class="fa fa-fw fa-bar-chart"></i>
              </a> --}}

            </td>
          </tr>
          @endforeach
          
        </tbody>
      </table>
  </div>
</div><!-- row ends -->