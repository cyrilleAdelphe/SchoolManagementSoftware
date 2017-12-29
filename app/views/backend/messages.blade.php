        <div class = "box-body">
            @if(Session::has('error-msg'))
            <div class = "alert alert-danger alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('error-msg'))}}">
                {{ Session::get('error-msg') }}
            </div>
            @endif
        </div>

        <div class = "box-body">
           @if(Session::has('success-msg'))
            <div class = "alert alert-success alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('success-msg'))}}">
                {{ Session::get('success-msg') }}
            </div>
            @endif
        </div>

        <div class = "box-body">
           @if(Session::has('info-msg'))
            <div class = "alert alert-info alert-dissmissable">
                <button type = "button" class = "close" data-dismiss = "alert">X</button>
                <input type = "hidden" class = "global-remove-url" value = "{{URL::route('remove-global', array('info-msg'))}}">
                {{ Session::get('info-msg') }}
            </div>
            @endif
        </div>