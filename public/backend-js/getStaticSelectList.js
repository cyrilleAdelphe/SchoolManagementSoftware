//boilerplate code for generate select list according to associative array {option_value:alias,...}
function getStaticSelectList(data, selected)
{
	//default value
	if (typeof(selected)==='undefined' || selected=='') selected = 0;

	var select = '';
	//if(Object.keys(data).length !=1 )
	{
		select = '<option value = "0"> -- Select -- </option>';	
	}
		
	for(var i in data)
	{
		var sel;
		if(selected == i)
				sel = 'selected';
		else
			sel = '';
		select += '<option value = '+i+' '+sel+'>'+data[i]+'</option>'+"\n";

	}

	return select;
}