                        <?php

                        /*echo '<pre>';
                        print_r($data);
                        die();*/
                        ?>
                        @foreach($data as $class_id => $d)
                        <div class = "grade-settings-data">
                            <div class = "row">
                              <div class = "col-md-3">
                                <button type="button" class = "btn btn-success btn-flat edit">Edit</button>
                              </div>
                            </div>
                            
                            <div class="info-bar">
                            @define $last_class = $d['classes']
                              <strong>Grading details of</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Session: {{AcademicSession::where('id', $session)->pluck('session_name')}} | From Class: {{Classes::where('id', $class_id)->pluck('class_name')}} @if(count($d['classes'])) 
                              | To Class: {{Classes::where('id', end($last_class))->pluck('class_name')}} 
                              @endif
                              <input  type = "hidden" class = "input-data-class-id" value = "{{$class_id}}">
                              @foreach($d['classes'] as $class)
                                <input class = "input-data-class-id" type = "hidden" value = "{{$class}}">
                              @endforeach
                              
                            </div>
                            <table  class="table table-bordered table-striped ">
                              <thead>
                                <tr>
                                  <th>SN</th>
                                  <th>From</th>
                                  <th>To</th>
                                  <th>Grade</th>
                                  <th>GPA</th>
                                  <th>Actions</th>
                                  
                                </tr>
                              </thead>
                              <tbody>
                                @define $index = 0
                                @foreach($d['data'] as $grades)
                                <tr>
                                  <td>{{++$index}}</td>
                                  <td class = "data"><p class = "input-text">{{$grades['from_percent']}}</p><input class = "input-data input-data-from-percent" type = "hidden" value = "{{$grades['from_percent']}}"></td>
                                  <td class = "data"><p class = "input-text">{{$grades['to_percent']}}</p><input class = "input-data input-data-to-percent" type = "hidden" value = "{{$grades['to_percent']}}"></td>
                                  <td class = "data"><p class = "input-text">{{$grades['grade']}}</p><input class = "input-data input-data-grade" type = "hidden" value = "{{$grades['grade']}}"></td>
                                  <td class = "data"><p class = "input-text">{{$grades['grade_point']}}</p><input class = "input-data input-data-grade-point" type = "hidden" value = "{{$grades['grade_point']}}"></td>
                                  <td class = "ajax-actions"></td>
                                  
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                            <!-- ends -->
                              <div class = "row">
                                <div class = "col-md-3">    
                                  <button type="button" class = "btn btn-primary btn-flat ajax-add-more" style="display:none;">Add More</button>

                                </div>
                              </div>
                              <div class = "row">
                                <div class = "col-md-3">    
                                  <button type="button" class = "btn btn-success btn-flat edit">Edit</button>

                                </div>
                              </div>
                            {{Form::token()}}
                            
                        </div>
                        @endforeach