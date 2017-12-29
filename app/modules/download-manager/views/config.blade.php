@extends('download-manager.views.main')

@section('tabs')
	<div class="tab-content">
	    <div class="tab-pane active" id="tab_active">
	    	<form method = "post" action = "{{URL::route('download-manager-config-post',array($parent['id'],$parent['google_file_id']))}}">
          <div class="form-row">
            <label for="max_show">Max downloads on frontend</label> 
            <input name = "max_show" id="max_show" class="form-control" type="text" placeholder="Enter number of articles" 
              value= "{{ (Input::old('max_show')) ? (Input::old('max_show')) :
                     ( isset($config) ? $config['max_show'] : '') }}">
            <span class = "form-error">
              @if($errors->has('max_show')) 
                {{ $errors->first('max_show') }} 
              @endif
            </span>
          </div>

          <div class="form-row">
            <button class="btn btn-primary" type="submit">Submit</button>
          </div>
          {{ Form::token() }}
        </form>

	    </div>
	</div>
@stop

@section('download-manager-scripts')
		
	<script type="text/javascript">
    	document.getElementById("config_tab").setAttribute("class", "active");
    </script>
    
@stop