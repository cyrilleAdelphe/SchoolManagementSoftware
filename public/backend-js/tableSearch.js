$(document).ready(function(){

	$('.search_column').keyup(function(e){

		var currentElement = $(this);
		if(e.which == 13)
		{

			//find column name
			var column_value = currentElement.val();
			var column_name = currentElement.parent().find('.field_name').val();
			var url = $('#current_url').val();


			var field_names = [];
			var field_values = [];
			//search for entire column names
			
			$( ".search_column" ).each(function( index ) {
			  	var current_element = $(this);
			  	if(current_element.val() != '')
				{
					field_names.push(current_element.parent().find('.field_name').val());
				  	field_values.push(current_element.val());
				}
			});

			var parameters = url;
			if(field_names.length > 0)
			{
				parameters = addGetParametersToUrl('', 'column_name', field_names);
				var temp = url + parameters;

				parameters = addGetParametersToUrl(temp, 'column_value', field_values);
			}
				
			
				window.location.replace(parameters);

		}
		else
		{

			var column_nums = [];
			var column_vals = [];
			$( ".search_column" ).each(function( index ) {
			  	var current_element = $(this);
			  	if(current_element.val() != '')
				{
					column_nums.push(current_element.attr('id'));
					column_vals.push(current_element.val());	
				}

				$('.search-table tr').each(function(index, val)
				{
					if(index != 0)
					{
						
						var currentRow = $(this);
						currentRow.show();
						$.each( column_nums, function( key, value ) 
						{
							var currentColumn = currentRow.find("td:eq("+ (parseInt(value)) +")");
							console.log(currentColumn.text().toLowerCase());
							console.log(column_vals[key].toLowerCase());
							if(currentColumn.text().toLowerCase().indexOf(column_vals[key].toLowerCase()) == -1)
							{
								currentRow.hide();
							}

						});
					}
				});	
			});	 
		}
	});

function getUrlParameter(address, sParam)
{
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

function addGetParametersToUrl(address, parameter_name, parameter_value)
{
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

	if (typeof page != 'undefined')
		result += 'page=' + page + '&';

	if (typeof status != 'undefined')
		result += 'status=' + status + '&';

	if (typeof column_name != 'undefined')
		result += 'column_name=' + column_name + '&';

	if (typeof column_value != 'undefined')
		result += 'column_value=' + column_value + '&';

	if (typeof paginate != 'undefined')
		result += 'paginate=' + paginate;

	return result;

}

});

