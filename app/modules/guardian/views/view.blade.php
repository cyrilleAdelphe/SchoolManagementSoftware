@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>Guardian View</h1>
@stop

@section('content')
             
	<div class="tab-pane active" id="tab_1">
	    @if($data['data'])
		    <div class="row">
                  <div class="col-sm-3" style="margin-bottom:15px">
                    <a  href="#" onclick="history.go(-1);" class="btn btn-danger"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
                </div><!-- row ends -->
                <div class="col-sm-3 col-sm-offset-6 col-xs-offest-1 col-xs-9" >
		        	<div class="btn-group pull-right">
						
						@if(Auth::user()->check() && Auth::user()->user()->role == 'guardian')
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
						@endif
					</div>
		        </div>
		        @if(Auth::user()->check() && Auth::user()->user()->role == 'guardian')
				    @include('guardian.views.change-password-modal')
					@include('guardian.views.edit-modal')
				@endif
            </div>
            <div class="row">
		    	<div class="col-sm-12">
		    		<div class="profile-image">
		    			@if(strlen(trim($data['data']->photo)))
						<img class="img-responsive" src = "{{Config::get('app.url').'app/modules/guardian/assets/images/'.$data['data']->photo}}">
						@else
							<img class="img-responsive"  class="img-responsive" src = "{{Config::get('app.url').'app/modules/guardian/assets/images/no-img.png'}}" >
						@endif	
		    		</div>
		    		<div class="profile-detail">
		    			<div class="main-head" style="text-align:center">
		    				{{$data['data']->guardian_name}}
		    			</div>
		    			<div class="second-head" style="text-align:center">
		    				<span style="color:#333">Username:</span> {{$data['data']->username}}
		    			</div>
		    			<ul>
		    				<li>
		    					<label class="pD">Email:</label> {{$data['data']->email}}
		    				</li>
		    				<li>
		    					<label class="pD">Current address:</label> {{$data['data']->current_address}}
		    				</li>
		    				<li>
		    					<label class="pD">Permanent address:</label> {{$data['data']->permanent_address}}
		    				</li>
		    				<li>
		    					<label class="pD">Primary contact:</label> {{$data['data']->primary_contact}}
		    				</li>
		    				<li>
		    					<label class="pD">Secondary contact:</label> {{$data['data']->secondary_contact}}
		    				</li>
		    				<li>
		    					<label class="pD">Active:</label> {{$data['data']->is_active}}
		    				</li>
		    			</ul>
		    		</div>
		    	</div>
		    </div>
					

				@define $i = 1;
				
				<div class="main-head">
	    		Related to
	    	</div>

				<table id="pageList" class="table table-bordered table-striped">
				 	<thead>
				 		<tr>
				 			<th>SN.</th>
				 			<th>Student's name</th>
				 			<th>Relation</th>
				 		</tr>
				 	</thead>

				 	<tbody>
					 		
					@foreach($data['related_students'] as $student)			
						<tr>
							<td>
								{{$i++}}
							</td>
							<td>
								<a href="{{ URL::route('student-view', $student->id) }}">{{$student->student_name}}</a>
							</td>
							<td>
								{{ $student->relationship }}
							</td>
						</tr>
					@endforeach
							
					</tbody>
				</table>
		@else
			<h1>Record Not Found</h1>
		@endif
	</div>
@stop