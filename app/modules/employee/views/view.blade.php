@extends('backend.'.$current_user->role.'.main')

@section('content')
             
<div class="tab-pane active" id="tab_1">
  	<div class="row">
        <div class="col-sm-3" style="margin-bottom:15px">
          <a  href="#" onclick="history.go(-1);" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
        </div><!-- row ends -->
        <div class="col-sm-3 col-sm-offset-6 col-xs-offest-1 col-xs-9" >
            <div class="btn-group pull-right">
                @if ($current_user->role == 'admin' && $current_user->user_id == $data['data']->id)
                )
                    <button class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        <i class="fa fa-fw fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#details">Edit details</a>
                        </li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#password">Change password</a>
                        </li>
                    </ul>
                @elseif ($current_user->role == 'superadmin')
                    <button class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        <i class="fa fa-fw fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                          <a href="#" data-toggle="modal" data-target="#password">Change password</a>
                        </li>
                    </ul>
                    @include('employee.views.change-password-modal')
                @endif
            </div>
        </div>
    </div>
    <div class="row">
    	<div class="profile-image">
				@if(strlen(trim($data['data']->photo)))
					<img class="img-responsive" src = "{{Config::get('app.url').'app/modules/employee/assets/images/'.$data['data']->photo}}">
				@else
					<img class="img-responsive" src = "{{Config::get('app.url').'app/modules/employee/assets/images/no-img.png'}}" >
				@endif
		</div>
    	<div class="main-head" style="text-align:center">
    		{{$data['data']->employee_name}}
    	</div>
    	<div class="second-head" style="text-align:center">
    		<span style="color:#333">Username:</span> {{$data['data']->username}}
    	</div>
		</div>
    <div class="row">
    	<div class="col-sm-7">
    		
    		<div class="profile-detail">
    			
    			<ul>
    				<li>
    					<label>Email:</label> {{$data['data']->email}}
    				</li>
    				<li>
    					<label>DOB:</label> A.D. {{$data['data']->employee_dob_in_ad}} ( B.S. {{$data['data']->employee_dob_in_bs}} )
    				</li>
    				<li>
    					<label>Gender:</label> {{$data['data']->sex}}
    				</li>
    				<li>
    					<label>Current address:</label> {{$data['data']->current_address}}
    				</li>
    				<li>
    					<label>Permanent address:</label> {{$data['data']->permanent_address}}
    				</li>
    				<li>
    					<label>Primary contact:</label> {{$data['data']->primary_contact}}
    				</li>
    				<li>
    					<label>Secondary contact:</label> {{$data['data']->secondary_contact}}
    				</li>
    				<li>
    					<label>Joining date:</label> A.D. {{$data['data']->joining_date_in_ad}} ( B.S. {{$data['data']->joining_date_in_bs}} )
    				</li>
    				<li>
    					<label>Working:</label> {{$data['data']->is_working}}
    				</li>
                    <li>
                        <label>Position:</label> {{$data['data']->position}}
                    </li>
    				@if($data['data']->is_working !== 'yes')
    					<li>
    						<label>Leaving date:</label> A.D. {{$data['data']->leaving_date_in_ad}}
    						 ( B.S. {{$data['data']->leaving_date_in_bs}} )
    					</li>
					@endif
    			</ul>
				@if(strlen(trim($data['data']->cv)))
					<a href = "{{Config::get('app.url').'app/modules/employee/assets/cv/'.$data['data']->cv}}" class="btn btn-block btn-flat btn-primary btn-lg">
						<i class="fa fa-fw fa-file-text-o "></i> View C.V.
					</a>
				@else
					<div class="alert alert-warning alert-dismissable">
						<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
						<h4>
						<i class="icon fa fa-warning"></i>
						Alert!
						</h4>
						No C.V. available .
					</div>
				@endif						
    		</div>
    	</div>
    	<div class="col-sm-5" style="margin-top:15px">
    		<!-- quick email widget -->
              <div class="box box-danger">
                <div class="box-header">
                  <i class="fa fa-envelope"></i>
                  <h3 class="box-title">Quick message</h3>
                  <!-- tools box -->
                </div>
                <div class="box-body">
                  <form action="#" method="post">
                    <div class="form-group">
                      <input type="text" class="form-control" name="subject" placeholder="Subject"/>
                    </div>
                    <div>
                      <textarea class="textarea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                    </div>
                  </form>
                </div>
                <div class="box-footer clearfix">
                  <button class=" btn btn-danger btn-flat" id="sendEmail">Send and notify <i class="fa fa-arrow-circle-right"></i></button>
                </div>
              </div>
          <!-- quick email ends -->
    	</div>
    </div>			
</div>
     
@stop