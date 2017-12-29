@extends('backend.'.$role.'.main')

@section('custom-css')
  <link href="{{asset('sms/assets/css/print.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
  <h1>Progress Report</h1>
@stop
      

@section('content')
  @if(count($data))
    <div class="row">
      <div class="col-sm-12" style="margin-bottom:15px">
        <a class="btn btn-default" target="_blank" onclick="printDiv('printableArea')" href="#">
            <i class="fa fa-print"></i>
            Print
        </a>
      </div>
    </div><!-- print row ends -->
    <div id="printableArea">
      <div class="row">
        <div class="col-sm-6">
          <h4 class="text-red">{{$data['student_name']}} - {{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0))}} {{HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0))}}</h4> 
        </div>
      </div> <!-- row ends -->

      <div class="row">
        <div class="col-sm-12">
          <table id="pageList" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>SN</th>
                <th>Subject</th>
                
                @define $config = json_decode(File::get(REPORT_CONFIG_FILE))

                @if($config->show_percentage == 'yes')
                  <th>Full Marks</th>
                  <th>Pass Marks</th>
                  <th>Marks Obtained</th>
                  @if($config->show_grade == 'yes')
                  <th>Grade</th>
                  @endif
                  @if($config->show_grade_point == 'yes')
                  <th>Grade Point</th>
                  @endif
                  <th>Practical Full Marks</th>
                  <th>Practial Pass Marks</th>
                  <th>Practical Marks Obtained</th>
                  @if($config->show_grade == 'yes')
                  <th>Grade</th>
                  @endif
                  @if($config->show_grade_point == 'yes')
                  <th>Grade Point</th>
                  @endif
                  <th>Theory + Practical</th>
                  @if($config->show_grade == 'yes')
                    <th>Grade</th>
                  @endif
                  @if($config->show_grade_point == 'yes')
                  <th>Grade Point</th>
                  @endif
                @endif

                @if($config->cas_percentage > 0)
                  <th>CAS</th>
                @endif
                
                @if($config->show_grade == 'yes')
                  <th>Grade</th>
                @endif
                
                @if($config->show_grade_point == 'yes')
                  <th>Grade Point</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @define $i = 1
              @foreach($data['subjects'] as $subject_id => $d)
              <tr>
                <td>{{$i++}}</td>
                <td>{{$d['subject_name']}}</td>
                @if($config->show_percentage == 'yes')
                  <td>{{ $d['full_marks'] }}</td>
                  <td>{{ $d['pass_marks'] }}</td>
                  <td>
                    @if($d['marks'] < $d['pass_marks']) 
                      <span class="text-red">{{$d['marks']}}</span> 
                    @else 
                      {{$d['marks']}} 
                    @endif
                  </td>
                  @if($config->show_grade == 'yes')
                  <td>{{$d['marks_grade']}}</td>
                  @endif
                  @if($config->show_grade_point == 'yes')
                  <td>{{$d['marks_grade_point']}}</td>
                  @endif
                  <td>{{ $d['practical_full_marks'] }}</td>
                  <td>{{ $d['practical_pass_marks'] }}</td>
                  <td>
                    @if($d['practical_marks'] < $d['practical_pass_marks']) 
                      <span class="text-red">{{$d['practical_marks']}}</span> 
                    @else 
                      {{$d['practical_marks']}} 
                    @endif
                  </td>
                  @if($config->show_grade == 'yes')
                  <td>{{$d['practical_marks_grade']}}</td>
                  @endif
                  @if($config->show_grade_point == 'yes')
                  <td>{{$d['practical_marks_grade_point']}}</td>
                  @endif
                  <td>{{$d['marks'] + $d['practical_marks']}}</td>
                @endif

                @if($config->show_grade == 'yes')
                  <td>{{$d['theory_and_practical_grade']}}</td>
                @endif

                @if($config->show_grade_point == 'yes')
                  <td>{{$d['theory_and_practical_grade_point']}}</td>
                @endif

                @if($config->cas_percentage > 0)
                  <td>
                  @if($d['cas_marks'] < $config->cas_pass_percentage) 
                      <span class="text-red">{{$d['cas_marks']}}</span> 
                    @else 
                      {{$d['cas_marks']}} 
                    @endif
                  </td>
                @endif

                @if($config->show_grade == 'yes')
                  <td>
                    {{$d['cas_grade']}}
                  </td>
                @endif

                @if($config->show_grade_point == 'yes')
                  <td>
                    {{$d['cas_grade_point']}}
                  </td>
                @endif

              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- row ends -->
      <div class="row">
        <div class="col-sm-4 col-sm-offset-7">
          <table class="table">
            <tr>
              <td><strong>Rank :</strong></td>
              <td> {{$data['rank']}} </td>
            </tr>
            
            @if($config->show_percentage == 'yes')
            <tr>
              <td><strong>Total :</strong></td>
              <td>{{$data['total_marks']}}</td>
            </tr>
            <tr>
              <td><strong>Status :</strong></td>
              <td><span class= @if($data['status'] == "Failed") "text-red" @else "text-green" @endif>{{$data['status']}}</span></td>
            </tr>
            <tr>
              <td><strong>Percentage :</strong></td>
              <td>{{$data['percentage']}}%</td>
            </tr>
            @endif
            
            @if($config->show_grade == 'yes')
            <tr>
              <td><strong>Grade :</strong></td>
              <td>
                {{
                  $data['cgpa_grade']
                }}
              </td>
            </tr>
            @endif

            @if($config->show_grade_point == 'yes')
            <tr>
              <td><strong>Grade Point Average :</strong></td>
              <td>{{ $data['cgpa'] }}</td>
            </tr>
            @endif

            <tr>
              <td><strong>Remark :</strong></td>
              <td> {{$data['remarks']}} </td>
            </tr>
          </table>
        </div>
      </div><!-- row ends -->
    </div><!-- printable div ends -->
  
  @else
   Student Report Not found
@endif
@stop

@section('custom-js')
    <script>
      function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
      }
    </script>
@stop