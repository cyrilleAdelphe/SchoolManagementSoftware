@extends('billing.views.tabs')


@section('custom-css')
<link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
  <h1>Billing - Create Fee Category</h1>
@stop

@section('tab-content')
<div class="row">
  <div class="col-sm-7">
    <form action = "{{URL::route('billing-create-fee-post')}}" method = "post">       
        <div class="form-group">
          <label for="fee_category">Fee category</label>
          <input id="fee_category" class="form-control" type="text" name = "fee_category" placeholder="Enter Fee Category">
        </div>  
        <div class="form-group">
          <label>
            Tax Applicable
          </label><br/>
          <label>
            <input type="radio" name="tax_applicable" class="minimal"  value = "yes"/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="tax_applicable" value = "no" class="minimal" checked/> No
          </label>
        </div>
        <div class="form-group">
          <label>Fee type</label>
          {{-- Change-code Billing-v1-changed-made-here --}}
          <!-- This is for Nepali
          <select class="form-control" id="fee_type" name = "fee_type">
            <option value="onetime">One time</option>
            <option value="recurring">Recurring</option>
            <option value="baishak">Baishak</option>
            <option value="jestha">Jestha</option>
            <option value="ashad">Ashad</option>
            <option value="shrawan">Sharwan</option>
            <option value="bhadra">Bhadra</option>
            <option value="ashwin">Ashwin</option>
            <option value="kartik">Kartik</option>
            <option value="mangsir">Mangshir</option>
            <option value="poush">Poush</option>
            <option value="magh">Margh</option>
            <option value="falgun">Falgun</option>
            <option value="chaitra">Chaitra</option>
          </select> -->
          <!-- this is for english
          <select class="form-control" id="fee_type" name = "fee_type">
            <option value="onetime">One time</option>
            <option value="recurring">Recurring</option>
            <option value="january">January</option>
            <option value="february">February</option>
            <option value="march">March</option>
            <option value="april">April</option>
            <option value="may">May</option>
            <option value="june">June</option>
            <option value="july">July</option>
            <option value="august">August</option>
            <option value="september">September</option>
            <option value="october">October</option>
            <option value="november">November</option>
            <option value="december">December</option>
          </select> -->
          <select class="form-control" id="fee_type" name = "fee_type">
            <option value="onetime">One time</option>
            <option value="recurring">Recurring</option>
            <option value="january">January</option>
            <option value="february">February</option>
            <option value="march">March</option>
            <option value="april">April</option>
            <option value="may">May</option>
            <option value="june">June</option>
            <option value="july">July</option>
            <option value="august">August</option>
            <option value="september">September</option>
            <option value="october">October</option>
            <option value="november">November</option>
            <option value="december">December</option>
          </select>
          {{-- Change-code Billing-v1-changed-made-here --}}
        </div>
        <div class="form-group">
          <label for="fullname">Description</label>
          <textarea class="textarea" name = "description" placeholder="Place some text here" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
        </div>
        <div class = "row" id = "fee-appicable-classes-sections-title" style = "display:none;">
            <div class = "col-md-2">
              <b>Session</b>
            </div>
            <div class = "col-md-3">
              <b>Class</b>
            </div>
            <div class = "col-md-2">
              <b>Section</b>
            </div>
            <div class = "col-md-3">
              <b>Amount</b>
            </div>
            <div class = "col-md-2">
            </div>
          </div>
        <div id = "fee-appicable-classes-sections" style = "display:none;">
          <div class = "fee-applicable-class-section row">
            <div class = "col-md-2 billBox">
              @define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();
              <select class = "academic_session_id" name = "academic_session_id[]">
                @foreach($sessions as $s)
                @if($s->is_current == 'yes')
                  @define $current_session = $s->id
                @endif
                <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
                @endforeach
              </select>
            </div>
            <div class = "col-md-3 billBox">
              <select class = "class_id" name = "class_id[]">
                @define $classes = Classes::where('academic_session_id', $current_session)->select('class_name', 'id')->get();
                @foreach($classes as $class)
                <option value = "{{$class->id}}"> {{$class->class_name}} </option>
                @endforeach
              </select>
            </div>
            <div class = "col-md-2 billBox">
              <select class = "section_id" name = "section_id[]">
                <option value = "all"> All </option>
              </select>
            </div>
            <div class = "col-md-3 billBox">
              <input class = "fee_amount" name = "fee_amount[]" type = "number" step=0.01>
            </div>
            <div class = " billBox remove-button col-md-2">
              <a data-toggle="tooltip" title="Remove" href = "#" class = "btn btn-danger btn-flat remove"> <i class="fa fa-fw fa-trash"></i></a>
            </div>
          </div>

        </div>
        <div class = "form-group add-button" style = "display:none;">
            <a href = "#" class = "btn btn-default btn-flat add-more"><i class="fa fa-plus"></i> Add more</a>
        </div>
        <button class="btn btn-flat btn-success btn-lg">Save </button>
        <button class="btn btn-flat btn-primary btn-lg " href="#">Save and new </button>

        <!-- Modal -->
    
    <!-- modal ends -->
    {{Form::token()}}
    <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
    <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
    <input type = "hidden" id = "billing-ajax-get-student-list" value = '{{URL::route('billing-ajax-get-student-list')}}'>
    </form>
    
  </div>
  <div class="col-sm-5">
                  <div class="bill-title">School fee list</div>
                  @if(count($fees))
                  <table id="pageList" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>SN</th>
                        <th>Fee title</th>
                        <th>Type</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @define $i = 1
                      @foreach($fees as $f)
                      <tr>
                        <td>{{$i++}}</td>
                        <td>{{$f->fee_category}}</td>
                        <td>{{$f->fee_type}}</td>
                        <td>
                          <a href="{{URL::route('billing-edit-fee-get', $f->id)}}" data-toggle="tooltip" title="Edit" class="btn btn-success btn-flat btn-sm" type="button">
                            <i class="fa fa-fw fa-edit"></i>
                          </a>
                          <a href="#" data-toggle="modal" data-target="#fee-delete{{$f->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
                            <i class="fa fa-fw fa-trash"></i>
                          </a>
                        </td>
                        @include('billing.views.modal.delete-fee')
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  @else
                  <div class="billAlert"><i class="fa fa-warning" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;No Fees Created</div>
                  @endif
                </div>
  
</div>
@stop

@section('custom-js')

<script src="{{asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
    
<script type="text/javascript">
      
      updateSectionList($('.section_id'));
      $(function () 
      {
        

        $(document).on('click', '.add-more', function(e)
        {
          e.preventDefault();
          var default_dynamic_fields = $('.fee-applicable-class-section').first().html();
              default_dynamic_fields = '<div class = "fee-applicable-class-section row">' + default_dynamic_fields + '</div>';

          $('#fee-appicable-classes-sections').append(default_dynamic_fields);
          
        });

        $(document).on('click', '.remove', function(e)
        {
          e.preventDefault();
          $(this).parent().parent().remove();
        });

        $('#fee_type').change(function(e)
        {
          if($(this).val() == 'onetime')
          {
            $('#fee-appicable-classes-sections').css('display', 'none');
            $('#fee-appicable-classes-sections-title').css('display', 'none');
            $('.add-button').css('display', 'none');
          }
          else
          {
            $('#fee-appicable-classes-sections').css('display', 'block'); 
            $('#fee-appicable-classes-sections').css('display', 'block');
            $('#fee-appicable-classes-sections-title').css('display', 'block');
            $('.add-button').css('display', 'block');
          }
        });

      });

      $(document).on('change', '.academic_session_id', function(e)
      {
        var currentElement = $(this);
        
        updateClassList(currentElement);
        //updateSectionList(currentElement);
        
      });

      $(document).on('change', '.class_id', function(e)
      {
        var currentElement = $(this);
        
        updateSectionList(currentElement);
      });


      function updateClassList(currentElement)
      {
        var currentRow = currentElement.parent().parent();

        var session_id = currentRow.find('.academic_session_id');
        var class_id = currentRow.find('.class_id');

        class_id.html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-class-list").val(),
            'method' : 'GET',
            'data' : {'academic_session_id' : session_id.val()}
        }).done(function(data)
        {
          class_id.html(data);
          updateSectionList(currentElement);
        });
      }

      function updateSectionList(currentElement)
      {
        var currentRow = currentElement.parent().parent();

        var class_id = currentRow.find('.class_id');
        var section_id = currentRow.find('.section_id');

        section_id.html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-section-list").val(),
            'method' : 'GET',
            'data' : {'class_id' : class_id.val()}
        }).done(function(data)
        {
          section_id.html(data);
        });
      }

    </script>
    <script type="text/javascript">
      $(function () {

        $("#checkboxG1").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

        $('#fee_type').change(function()
        {
            if (this.value !== 'onetime')
            {
                 $('#type').modal('show');
            }
        });
        // Replace the <textarea id="editor1"> with a CKEditor
        $(".textarea").wysihtml5();
        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
        });
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
          checkboxClass: 'icheckbox_minimal-red',
          radioClass: 'iradio_minimal-red'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        });
      });
    </script>
@stop