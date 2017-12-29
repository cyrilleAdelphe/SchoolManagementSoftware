$(document).ready(function()
		{
			$('.close').click(function(){
				//alert('here');
				var url = $(this).parent().find('.global-remove-url').val();
				
				var request = $.ajax
								({
									type: 'POST',
									url: url
								});

			});
		});