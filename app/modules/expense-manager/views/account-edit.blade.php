@extends('backend.'.$current_user->role.'.submain')
@section('content')
  <div class="content"> 
      <form action= "{{ URL::route('edit-account', $account->id) }}" method="POST" target="_top">
        <div class="form-group">
          <label>Account name *</label>
          <input class="form-control" type="text" name="account_name" value="{{ $account->account_name}}" required>
        </div><div style="color:red;">{{ $errors->first('account_name')}}</div>
        <!-- income setup -->
       
         <div class="form-group">
          <label>Balance *</label>
          <input class="form-control" type="text" name="balance" value="{{ $account->balance}}" required>

        </div><div style="color:red;">{{ $errors->first('balance')}}</div>
        <div class="form-group">
          <label for="content">Description</label>
          <textarea class="textarea" name = "description" placeholder="Insert your note here" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ $account->description }}</textarea>
        </div>
        <div><button class="btn btn-lg btn-primary btn-flat">Update account</button></div>
        <br/>
        {{Form::token()}}
      </form>
    </div>

@section('custom-js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
     $(document).ready(function(){
        var sum = 0;

        function updateSum(){
          sum = 0;
          $('input:checked').each(function(){
          sum = sum + parseFloat($(this).val());
          });
          var totalIn$ = '$' + sum;
          $('#sumTotal').text(totalIn$);
        }
       
        $('input').change(function(){
          updateSum();
        });
        
        updateSum();
      })
    </script>
        <script type="text/javascript">
      
      setTimeout(function() {
        $('.alert-success').fadeOut('slow');
        }, 2000);
     </script>
    
@stop
@stop
    
