@extends('backend.'.$role.'.main')

@section('custom-css')
  
@stop

@section('page-header')
    <h1>
      View book
      
    </h1>
@stop

@section('content')
  @if($data)
    <table class = "table table-striped table-hover table-bordered">
      <tbody>
        <tr>
          <th>Book Title :</th>
          <td>{{$data->title}}</td>
        </tr>
        <tr>
          <th>Author :</th>
          <td>{{$data->author}}</td>
        </tr>
        <tr>
          <th>Published date :</th>
          <td>{{$data->published_date}}</td>
        </tr>
        <tr>
          <th>Price :</th>
          <td>{{$data->price}}</td>
        </tr>
        <tr>
          <th>Number of copies :</th>
          <td>{{$data->no_of_copies}}</td>
        </tr>
        <tr>
          <th>Book IDs :</th>
          <td>{{$data->book_ids}}</td>
        </tr>
        <tr>
          <th>Maximum holding days :</th>
          <td>{{$data->max_holding_days}}</td>
        </tr>
        @if($data->image)
          <tr>
            <th>Image :</th>
            <td><img height="auto" width="250px" src="{{Config::get('app.url').'public/sms/assets/img/books/medium/'.$data->image}}"></td>
          </tr>
        @endif
        <tr>
          <th>Description :</th>
          <td>{{$data->description}}</td>
        </tr>
      </tbody>
    </table>
  @endif                 
@stop

@section('custom-js')
  <script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
  
  <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>


  <script type="text/javascript">
    $(function () {

      $("#datemask").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
      $("[data-mask]").inputmask();
      $(".textarea").wysihtml5();

    });
  </script>
@stop