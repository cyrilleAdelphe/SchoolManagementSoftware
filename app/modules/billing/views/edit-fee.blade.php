@extends('backend.'.$role.'.main')

@section('custom-css')
<link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
  <h1>Billing - Edit Fee Category</h1>
@stop


@section('content')
<div class="row">
  <div class="col-sm-12">
    <form action = "{{URL::route('billing-edit-fee-post', $id)}}" method = "post">       
        <div class="form-group">
          <label for="fee_category">Fee category</label>
          <input id="fee_category" class="form-control" type="text" name = "fee_category" placeholder="Enter Fee Category" value = "{{$data->fee_category}}">
        </div>  
        <div class="form-group">
          <label>
            Tax Applicable
          </label><br/>
          <label>
            <input type="radio" name="tax_applicable" class="minimal"  value = "yes" @if($data->tax_applicable == 'yes') checked @endif/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="tax_applicable" value = "no" class="minimal" @if($data->tax_applicable == 'no') checked @endif/> No
          </label>
        </div>
        <div class="form-group">
          <label>Fee type</label>
          {{-- Change-code Billing-v1-changed-made-here --}}
          <!-- this is For Nepali 
          <select class="form-control" id="fee_type" name = "fee_type">
            <option value="onetime" @if($data->fee_type == 'onetime') selected @endif>One time</option>
            <option value="recurring" @if($data->fee_type == 'recurring') selected @endif>Recurring</option>
            <option value="baishak" @if($data->fee_type == 'baishak') selected @endif>Baishak</option>
            <option value="jestha" @if($data->fee_type == 'jestha') selected @endif>Jestha</option>
            <option value="ashad" @if($data->fee_type == 'ashad') selected @endif>Ashad</option>
            <option value="shrawan" @if($data->fee_type == 'shrawan') selected @endif>Sharwan</option>
            <option value="bhadra" @if($data->fee_type == 'bhadra') selected @endif>Bhadra</option>
            <option value="ashwin" @if($data->fee_type == 'ashwin') selected @endif>Ashwin</option>
            <option value="kartik" @if($data->fee_type == 'kartik') selected @endif>Kartik</option>
            <option value="mangsir" @if($data->fee_type == 'mangsir') selected @endif>Mangshir</option>
            <option value="poush" @if($data->fee_type == 'poush') selected @endif>Poush</option>
            <option value="magh" @if($data->fee_type == 'magh') selected @endif>Margh</option>
            <option value="falgun" @if($data->fee_type == 'falgun') selected @endif>Falgun</option>
            <option value="chaitra" @if($data->fee_type == 'chaitra') selected @endif>Chaitra</option>
          </select>
          -->
          <!-- this is for english
          <select class="form-control" id="fee_type" name = "fee_type">
          <option value="onetime" @if($data->fee_type == 'onetime') selected @endif>One time</option>
            <option value="recurring" @if($data->fee_type == 'recurring') selected @endif>Recurring</option>
            <option value="january" @if($data->fee_type == 'january') selected @endif>January</option>
            <option value="february" @if($data->fee_type == 'february') selected @endif>February</option>
            <option value="march" @if($data->fee_type == 'march') selected @endif>March</option>
            <option value="april" @if($data->fee_type == 'april') selected @endif>April</option>
            <option value="may" @if($data->fee_type == 'may') selected @endif>May</option>
            <option value="june" @if($data->fee_type == 'june') selected @endif>June</option>
            <option value="july" @if($data->fee_type == 'july') selected @endif>July</option>
            <option value="august" @if($data->fee_type == 'august') selected @endif>August</option>
            <option value="september" @if($data->fee_type == 'september') selected @endif>September</option>
            <option value="october" @if($data->fee_type == 'october') selected @endif>October</option>
            <option value="november" @if($data->fee_type == 'november') selected @endif>November</option>
            <option value="december" @if($data->fee_type == 'december') selected @endif>December</option>
          </select> -->
          {{-- Change-code Billing-v1-changed-made-here --}}
          <select class="form-control" id="fee_type" name = "fee_type">
          <option value="onetime" @if($data->fee_type == 'onetime') selected @endif>One time</option>
            <option value="recurring" @if($data->fee_type == 'recurring') selected @endif>Recurring</option>
            <option value="january" @if($data->fee_type == 'january') selected @endif>January</option>
            <option value="february" @if($data->fee_type == 'february') selected @endif>February</option>
            <option value="march" @if($data->fee_type == 'march') selected @endif>March</option>
            <option value="april" @if($data->fee_type == 'april') selected @endif>April</option>
            <option value="may" @if($data->fee_type == 'may') selected @endif>May</option>
            <option value="june" @if($data->fee_type == 'june') selected @endif>June</option>
            <option value="july" @if($data->fee_type == 'july') selected @endif>July</option>
            <option value="august" @if($data->fee_type == 'august') selected @endif>August</option>
            <option value="september" @if($data->fee_type == 'september') selected @endif>September</option>
            <option value="october" @if($data->fee_type == 'october') selected @endif>October</option>
            <option value="november" @if($data->fee_type == 'november') selected @endif>November</option>
            <option value="december" @if($data->fee_type == 'december') selected @endif>December</option>
          </select>
        </div>
        <div class="form-group">
          <label for="fullname">Description</label>
          <textarea class="textarea" name = "description" placeholder="Place some text here" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{$data->description}}</textarea>
        </div>

        @if(count($data->studentFee))
        <div id = "fee-appicable-classes-sections">
        
          @foreach($data->studentFee as $s)
          <div class = "fee-applicable-class-section row">
            <div class = "col-md-2 billBox">
              @define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();

              @define $current_session = Classes::where('id', $s->class_id)->pluck('academic_session_id');

              <select class = "academic_session_id" name = "academic_session_id[]">
                @foreach($sessions as $ses)
                <option value = "{{$s->id}}" @if($ses->id == $current_session) selected @endif>{{$ses->session_name}}</option>
                @endforeach
              </select>
            </div>
            <div class = "col-md-2 billBox">
              <select class = "class_id" name = "class_id[]">
                @define $classes = Classes::where('academic_session_id', $current_session)->select('class_name', 'id')->get();
                @foreach($classes as $class)
                <option value = "{{$class->id}}" @if($class->id == $s->class_id) selected @endif> {{$class->class_name}} </option>
                @endforeach
              </select>
            </div>
            <div class = "col-md-2 billBox">
              @define $section_table = Section::getTableName();
              	@define $class_section_table = ClassSection::getTableName();

              	@define $sections = DB::table($class_section_table)->join($section_table, $section_table.'.section_code', '=', $class_section_table.'.section_code')->where($section_table.'.is_active', 'yes')->where($class_section_table.'.is_active', 'yes')->where('class_id', $s->class_id)->select($section_table.'.id', $section_table.'.section_code')->get();
                
              <select class = "section_id" name = "section_id[]">
              	<option value = "all"> All </option>

                @foreach($sections as $sec)
                <option value = "{{$sec->id}}" @if($s->section_id == $sec->id) selected @endif>{{$sec->section_code}}</option>
                <h1>{{$s->section_id}}</h1>
                @endforeach
              </select>
            </div>
            <div class = "col-md-3 billBox">
              <input class = "fee_amount" name = "fee_amount[]" value = "{{$s->fee_amount}}">
            </div>
            <div class = "remove-button col-md-3">
              <a href = "#" class = "btn btn-danger remove">Remove</a>
            </div>
          </div>
        @endforeach
        </div>
        <div class = "add-button">
          <a href = "#" class = "btn btn-default add-more">Add</a>
        </div>
        @else
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
        <div class = "add-button" style = "display:none;">
          <a href = "#" class = "btn btn-default add-more">Add</a>
        </div>      
        @endif

        
        <button class="btn btn-flat btn-success btn-lg">Edit </button>
        <button class="btn btn-flat btn-primary btn-lg " href="#">Save and new </button>

    {{Form::token()}}
    <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
    <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
    <input type = "hidden" id = "billing-ajax-get-student-list" value = '{{URL::route('billing-ajax-get-student-list')}}'>
    </form>
    
  </div>
  
</div>
@stop


@section('custom-js')

<script src="{{asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
    
<script type="text/javascript">
      
      $(function () 
      {
        $(document).on('click', '.add-more', function(e)
        {
          e.preventDefault();
          var default_dynamic_fields = $('.fee-applicable-class-section').first().html();
          $('#fee-appicable-classes-sections').append('<div class = "fee-applicalbe-class-section row">'+default_dynamic_fields+'<div>');
          
        });

        $(document).on('click', '.remove', function(e)
        {
          e.preventDefault();
          $(this).parent().parent().remove();
        });

      });

      $(document).on('change', '.academic_session_id', function(e)
      {
        var currentElement = $(this);
        
        updateClassList(currentElement);
        
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

    </script>

@stop