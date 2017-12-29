<div class="tab-pane " id="tab_2">
	<div class = "table-responsive">
    <table class = 'table table-striped table-hover table-bordered scrollable'>
			{{$tableHeaders}}
			<tbody class = 'search-table'>
				<?php $i = 1; ?>
				{{$searchColumns}}
				@if($data['count'])
					@foreach($data['data'] as $d)
						<tr>
							<td>{{$i++}}</td>
							<td>{{$d->filename}}</td>
							<td>
								<a href="{{$d->download_link}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="Download" @if(!AccessController::checkPermission('student-document', 'can_view')) disabled @endif>
                	<i class="fa fa-fw fa-download"></i>
                </a>
                <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('student-document', 'can_delete')) disabled @endif>
	                <i class="fa fa-fw fa-trash"></i>
	              </a>
	              @include('include.modal-delete')
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

<div class = "container">
	<div class = 'paginate'>
		@if($data['count'])
			{{$data['data']->appends($queryString)->links()}}
		@endif
	</div>
</div>

<script>
	// the only difference from the origin implementation in tableSearch.js is that student_username and _token parameters are added!!!

	$('.search_column').keyup(function(e) {
		var currentElement = $(this);
		if(e.which == 13) {
			//find column name
			var column_value = currentElement.val();
			var column_name = currentElement.parent().find('.field_name').val();
			var url = $('#current_url').val();

			var parameters = addGetParametersToUrl('', 'column_name', column_name);
			var temp = url + parameters;
			parameters = addGetParametersToUrl(temp, 'column_value', column_value);

			// added for this module
			temp = url + parameters;
			parameters = addGetParametersToUrl(temp, 'student_username', "{{ Input::get('student_username', 0) }}");
			temp = url + parameters;
			parameters = addGetParametersToUrl(temp, '_token', "{{ csrf_token() }}");
		
			window.location.replace(parameters);

		}
	});

	// same as the origin implementation in tableSearch.js
	function getUrlParameter(address, sParam) {
	    var sPageURL;

	    if(address == '')
	    	sPageURL = window.location.search.substring(1);
	    else
	    	sPageURL = address.substring(parseInt(address.indexOf('?')) + 1);

	    var sURLVariables = sPageURL.split('&');
	    for (var i = 0; i < sURLVariables.length; i++) 
	    {
	        var sParameterName = sURLVariables[i].split('=');
	        if (sParameterName[0] == sParam) 
	        {
	            return sParameterName[1];
	        }
	    }
	}

	// simple modifications
	function addGetParametersToUrl(address, parameter_name, parameter_value) {
		var result = '?';
		var url;

		if(address == '')
			url = window.location.href;
		else
			url = address;

		var page = getUrlParameter(address, 'page');
		
		var status = getUrlParameter(address, 'status');
		
		var paginate = getUrlParameter(address, 'paginate');
		
		var column_name = getUrlParameter(address, 'column_name');

		var column_value = getUrlParameter(address, 'column_value');

		// these lines were added for this module
		var student_username = getUrlParameter(address, 'student_username');
		var token = getUrlParameter(address, '_token');

		if(parameter_name == 'page')
			page = parameter_value;
		else if(parameter_name == 'status')
			status = parameter_value;
		else if(parameter_name == 'paginate')
			paginate = parameter_value;
		else if(parameter_name == 'column_name')
			column_name = parameter_value;
		else if(parameter_name == 'column_value')
			column_value = parameter_value;
		// these lines were added for this module
		else if (parameter_name == 'student_username')
			student_username = parameter_value
		else if (parameter_name == '_token')
			token = parameter_value

		if (typeof page != 'undefined')
			result += 'page=' + page + '&';

		if (typeof status != 'undefined')
			result += 'status=' + status + '&';

		if (typeof column_name != 'undefined')
			result += 'column_name=' + column_name + '&';

		if (typeof column_value != 'undefined')
			result += 'column_value=' + column_value + '&';

		if (typeof paginate != 'undefined')
			result += 'paginate=' + paginate + '&';

		// these lines were added for this module
		if (typeof student_username != 'undefined')
			result += 'student_username=' + student_username + '&';

		if (typeof token != 'undefined')
			result += '_token=' + token + '&';

		return result;

	}
</script>