$(function()
{
    $('.change-password').click(function(e)
    {
        var message = $(this).attr('data-alert-message');

        if(typeof message === typeof undefined | message === false) 
        {
            message = "Do you want reset password";
        }

        e.preventDefault();
        if (!confirm(message))
        {
          return false;
        }
        else
        {
            $(this).attr('disabled', true);
            $(this).find('.change-password-form').submit();
            //$(this).parent().find('form').submit();
        }
        
    });
});
