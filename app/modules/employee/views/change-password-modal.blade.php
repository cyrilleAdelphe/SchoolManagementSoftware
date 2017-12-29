<!-- Password Modal -->
<div id="password" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Change Password</h4>
      </div>
     
        <form method="POST" action="{{ URL::route('employee-change-password-by-superadmin-post', $data['data']->id) }}">
        	<div class="modal-body">
	      	  <div class="form-group">
	            <input name="new_password" id="fullname" class="form-control" type="password" placeholder="New password" />
	          </div>
	          <div class="form-group">
	              <input name="new_password_confirm" id="fullname" class="form-control" type="password" placeholder="Re-enter password" />
	          </div>
	        </div>
	        {{ Form::token() }}
	          <div class="modal-footer">
			    <button class="btn btn-danger pull-left btn-flat" data-dismiss="modal" type="button">Close</button>
				<button class="btn btn-success btn-flat" type="submit">Save changes</button>
			  </div>

	    </form>
			      
    </div>

  </div>
</div>