<div class="main-head" style="margin-top:15px" >
	General Documents
</div>

<div class="tab-pane " id="tab_2">
	<div class = "table-responsive">
    <table class = 'table table-striped table-hover table-bordered scrollable'>
			<tr>
				<th>S.No.</th>
				<th>Filename</th>
				<th>Action</th>
			</tr>
			<tbody class = 'search-table'>
				<?php $i = 1; ?>
				@if(count($documents))
					@foreach($documents as $d)
						<tr>
							<td>{{$i++}}</td>
							<td>{{$d->filename}}</td>
							<td>
								<a href="{{$d->download_link}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="Download" >
                	<i class="fa fa-fw fa-download"></i>
                </a>
                <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('student-document', 'can_delete')) disabled @endif>
	                <i class="fa fa-fw fa-trash"></i>
	              </a>
	              @include('include.modal-delete', array('module_name' => 'student-document'))
              </td>
						</tr>
					@endforeach
				@else
					<div class="alert alert-warning alert-dismissable">
						<h4>
							<i class="icon fa fa-warning"></i>No Data Found
						</h4>
					</div>
				@endif
				</tbody>
			
		</table>
	</div>
</div> 

<!-- quick email widget -->
<div class="box box-info">
  <div class="box-header">
    <i class="fa fa-envelope"></i>
    <h3 class="box-title">Message to parents</h3>
    <!-- tools box -->
  </div>
  <div class="box-body">
    <form action="#" method="post">
      <div class="form-group">
        <input type="text" class="form-control" id = "message_subject" name="message_subject" placeholder="Subject"/>
      </div>
      <div>
        <textarea class="textarea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name = "message"  id = "message"></textarea>
      </div>
    </form>
  </div>
  <div class="box-footer clearfix">
    <button class=" btn btn-success btn-flat" id="sendEmail">Send and notify <i class="fa fa-arrow-circle-right"></i></button>
  </div>
</div>
<!-- quick email ends -->

<script>
	
	$('#sendEmail').click(function()
  {
  	// this array hold the status of api call for sendMessage
  	var return_array = [];

  	var guardians = $.parseJSON('{{ json_encode($guardians) }}');

  	if (guardians.length == 0 ) {
  		alert('no parent associated with the student');
  	}
  	
    $.each(guardians, function(index, guardian) {
	    var current_status = {
	    		'guardian_name': guardian.guardian_name
	    };

	    var message_from_group = '{{ $role }}';
	    var message_from_id = '{{ $current_user->id }}';
	    var message = $('#message').val();
	    
	    var message_subject = $('#message_subject').val();
	    var message_to_group = 'guardian';
	    var token = '{{csrf_token()}}';

	    var message_to_username = guardian.username;

	    $.ajax({
	              "url": "{{URL::route('message-api-send')}}",
	              "data": {
		              				"message_from_group" : message_from_group, 
		              				"message_from_id" : message_from_id, 
		              				"message" : message, 
		              				"message_subject" : message_subject, 
		              				"message_to_group" : message_to_group, 
		              				"message_to_username" : message_to_username, 
		              				"_token" : token, 
		              				'is_viewed' : 'no', 
		              				'is_active' : 'yes'
	              				},
	              //"dataType" : "json",
	              "method": "POST",
	            }).done(function(data) {
						      data = $.parseJSON(data);
						      console.log(data);
						      if(data.status == 'success') {
						        current_status.status = true;
						      }
						      else {
						        current_status.status = false;
						      }
						      return_array.push(current_status);
						      console.log(current_status);
						      if (return_array.length == guardians.length) {
						      	// all api calls have been completed

						      	$('#message').val('');
	                  $('#message_subject').val('');
	                  var outputString = '';
	                  $.each(return_array, function(index, status) {
	                  	outputString += status.guardian_name + ': ' + (status.status ? 'sent' : 'not sent') + "\n";
	                  });
	                  alert(outputString);
	                }
						  });
		});

  });

</script>