<!-- Password Modal -->
<div id="change-password" class="modal fade" role="dialog">
  <div class="modal-dialog">
  	<!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Change Details</h4>
      </div>
     
      {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
      <form method="POST" action="{{ URL::route('admin-change-details-post') }}" enctype = "multipart/form-data">
      {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
      	<div class="modal-body">
      		<div class="form-group">
            {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
                {{-- Contents removed from here --}}
            {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
          </div>
      	  <div class="form-group">
            <input name="old_password" id="fullname" class="form-control" type="password" placeholder="Current password" />
          </div>
          <div class="form-group">
            <input name="new_password" id="fullname" class="form-control" type="password" placeholder="New password" />
          </div>
          <div class="form-group">
              <input name="new_password_confirm" id="fullname" class="form-control" type="password" placeholder="Re-enter password" />
          </div>
          <div class = 'form-group'>
						<label for = 'photo'  class = 'control-label'>Photo :</label>
						<br/>
            {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
            @define $photo = Employee::where('id', Auth::admin()->user()->admin_details_id)->pluck('photo');
						@if(strlen($photo))
							<img src = "{{Config::get('app.url').'app/modules/employee/assets/images/'. $photo}}" width="250px" height="auto">
						@else
							<p>No image selected</p>
						@endif
            {{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
						<br/><br/>
						<input type = 'file' name = 'photo'>
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