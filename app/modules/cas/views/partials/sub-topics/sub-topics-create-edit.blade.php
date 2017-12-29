<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Create Fee | Eton</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="{{asset('sms/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />       
    <!-- FontAwesome 4.3.0 -->
    <link href="{{asset('sms/assets/css/font-awesome.css')}}" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />    
    <!-- Theme style -->
    <link href="{{asset('sms/assets/css/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
    
  </head>
  <body>
    <div class="content"> 
      <div class="mTitle" style="margin-bottom: 15px">
        Sub Topics of {{Subject::where('id', $subject_id)->pluck('subject_name')}}
      </div>
      <div class="row">
        <div class="col-sm-6">
          @if(count($data))
          <form action = "{{URL::route('cas-sub-topics-create-edit-post', $subject_id)}}" method = "post" class = "table-responsive">
          <div class = "form-group">
            <input type = "Submit" class = "dynamic-edit btn btn-flat btn-succcess" value = "Edit">
          </div>
          <div class = "table-reponsive">
          <table  class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>SN</th>
                <th>Sub Topic</th>
                <th>Topic Description</th>
                <th width=10%>Weightage</th>
                <th>Full Marks</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
                @define $i = 0
                @foreach($data as $d)
                <tr>
                  <td>{{++$i}}</td>
                  <td class = "input-data-topic-name">{{$d->topic_name}}</td>
                  <td class = "input-data-topic-description">{{$d->topic_description}}</td>
                  <td class = "input-data-topic-weightage">{{$d->weightage}}</td>
                  <td class = "input-data-topic-full">{{$d->full_marks}}</td>
                  <td class = "actions"></td>
                  <input type = "hidden" name = "id[]" value = "{{$d->id}}">
                </tr>
                @endforeach
            </tbody>
          </table>
          </div>
            {{Form::token()}}
            <div class = "form-group">
              <a href = "#" class = "dynamic-add-more btn btn-flat btn-info"  style = "display: none">Add More</a>
            </div>

            <div class = "form-group">
              <input type = "Submit" class = "dynamic-edit btn btn-flat btn-succcess" value = "Edit">
            </div>
          </form>
          @else
            <h1>No Sub Topics Found</h1>
          @endif
        </div>
        
        <div class = "col-sm-6">
          <form action = "{{URL::route('cas-sub-topics-create-edit-post', $subject_id)}}" method = "post">
            <div id = "ajax-content">
              <div class = "dynamic_content">
                <div class="form-group">
                  <label for="topic_name">Topic Name</label>
                  <input id="topic_name" class="form-control" type="text" name = "topic_name[]" placeholder="e.g. Reading, Writing">
                </div>

                <div class="form-group">
                  <label for="topic_description">Topic Description</label>
                  <textarea id="topic_description" class="form-control" name = "topic_description[]"></textarea>
                </div>

                <div class="form-group">
                  <label for="topic_weightage">Weightage</label>
                  <input id="topic_weightage" class="form-control" name = "topic_weightage[]">
                </div>

                <div class="form-group">
                  <label for="topic_full">Full Marks</label>
                  <input id="topic_full" class="form-control" name = "topic_full[]">
                </div>


                <div class = "form-group">
                  <a href = "#" class = "btn btn-info btn-flat add-more">Add More</a>
                </div>
              </div>
            </div>
            
            {{Form::token()}}

            <div class = "form-group">
              <input type = "submit" value = "create" class = "btn btn-success btn-flat">
            </div>
          </form>

        </div>
      </div>
    </div>
   
    <!-- jQuery 2.1.4 -->
    <script src="{{asset('sms/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('sms/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>

    <script>
      $(function()
      {
        $(document).on('click', '.add-more', function(e)
        {
          e.preventDefault();
          var current_element = $(this);
          current_element.removeClass('btn-info');
          current_element.addClass('btn-danger');
          current_element.text('Remove');
          current_element.removeClass('add-more');
          current_element.addClass('remove');

          $('#ajax-content').append('<div class = "dynamic_content"><div class="form-group"><label for="topic_name">Topic Name</label><input id="topic_name" class="form-control" type="text" name = "topic_name[]" placeholder="e.g. Reading, Writing"></div><div class="form-group"><label for="topic_name">Topic Description</label><textarea id="topic_description" class="form-control" name = "topic_description[]"></textarea></div><div class="form-group"><label for="topic_weightage">Weightage</label><input id="topic_weightage" class="form-control" name = "topic_weightage[]"></div><div class="form-group"><label for="topic_full">Full Marks</label><input id="topic_full" class="form-control" name = "topic_full[]"></div><div class = "form-group"><a href = "#" class = "btn btn-info btn-flat add-more">Add More</a></div></div>');
        });

        $(document).on('click', '.remove', function(e)
        {
          e.preventDefault();
          var current_element = $(this);
          current_element.removeClass('btn-danger');
          current_element.addClass('btn-info');
          current_element.text('Add More');
          current_element.removeClass('remove');
          current_element.addClass('add-more');

          current_element.parent().parent().remove();
        });

        $(document).on('click', '.dynamic-edit', function(e)
        {
          e.preventDefault();
          var current_element = $('.dynamic-edit');
          $('.dynamic-add-more').css('display', 'block');

          $(current_element).each(function(index)
          {
            $(this).removeClass('btn-success');
            $(this).addClass('btn-danger');
            $(this).attr('value', 'Save');
            $(this).removeClass('dynamic-edit');
            $(this).addClass('dynamic-save');
          });

          var data = $('.input-data-topic-name');

          $(data).each(function(index)
          {
            var value = $(this).text();
            $(this).html('<input type = "text" name = "topic_name[]" value = "' + value + '">');
          });

          var data = $('.input-data-topic-description');

          $(data).each(function(index)
          {
            var value = $(this).text();
            $(this).html('<input type = "text" name = "topic_description[]" value = "' + value + '">');
          });


          var data = $('.input-data-topic-weightage');
          $(data).each(function(index)
          {
            var value = $(this).text();
            $(this).html('<input type = "number" name = "topic_weightage[]" value = "' + value + '">');
          });

          var data = $('.input-data-topic-full');
          $(data).each(function(index)
          {
            var value = $(this).text();
            $(this).html('<input type = "number" name = "topic_full[]" value = "' + value + '">');
          });

          var data = $('.actions');
          $(data).each(function(index)
          {
            $(this).html('<a href = "#" class = "btn btn-danger btn-flat dynamic-row-remove"> Remove </a>');
          });
          
        });

        $(document).on('click', '.dynamic-row-remove', function(e)
        {
          e.preventDefault();
          $(this).parent().parent().remove();
        });

        $(document).on('click', '.dynamic-add-more', function(e)
        {
          e.preventDefault();
          $('tbody').append('<tr><td></td><td class = "input-data-topic-name"><input type = "text" name = "topic_name[]"></td><td class = "input-data-topic-description"><input type = "text" name = "topic_description[]"</td><td class = "input-data-topic-weightage"><input type = "number" name = "topic_weightage[]"</td><td class = "input-data-topic-full"><input type = "number" name = "topic_full[]"</td><td class = "actions"><a href = "#" class = "btn btn-danger btn-flat dynamic-row-remove"> Remove </a></td><input type = "hidden" name = "id[]" value = "0"></tr>');

        });

      });
    </script>

  </body>
</html>