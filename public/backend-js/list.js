$(document).ready(function()
{
	$('#paginate10').click(function(e)
	{
		e.preventDefault();
		var parameters = addGetParametersToUrl('paginate', '10');
		window.location.replace($('#current_url').val() + parameters);
	});

	$('#paginate20').click(function(e)
	{
		e.preventDefault();
		var parameters = addGetParametersToUrl('paginate', '20');
		window.location.replace($('#current_url').val() + parameters);
	});

	$('#paginate30').click(function(e)
	{
		e.preventDefault();
		var parameters = addGetParametersToUrl('paginate', '30');
		window.location.replace($('#current_url').val() + parameters);
	});

	$('#list_status').change(function(e)
	{
		e.preventDefault();
		var parameters = addGetParametersToUrl('status', $(this).val());
		window.location.replace($('#current_url').val() + parameters);
	});

});


function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
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

function addGetParametersToUrl(parameter_name, parameter_value)
{
	var result = '?';
	var url = window.location.href;

	var page = getUrlParameter('page');
	
	var status = getUrlParameter('status');
	
	var paginate = getUrlParameter('paginate');
	
	var column_name = getUrlParameter('column_name');

	var column_value = getUrlParameter('column_value');

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