@extends('download-manager.views.main')

@section('tabs')
	<div class="tab-content">
	    <div class="tab-pane active" id="tab_active">
	    	<form method="post" action="{{URL::route('download-manager-backend-folder-create-post')}}">
                <div class="form-group">
                  <label for="catname">Category name</label>
                  <input id="catname" name="filename" class="form-control" type="text" placeholder="Enter unique category name"
                    value= "{{ (Input::old('filename')) ? (Input::old('filename')) : '' }}">
                    <span class = "form-error">
                      @if($errors->has('filename')) 
                        {{ $errors->first('filename') }} 
                      @endif
                    </span>
                </div>
                <div class="form-group">
                    <label for="content">Description</label>
                    <textarea class="textarea" name="description" placeholder="Describe your category" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('description')) ? (Input::old('description')) : '' }}</textarea>

                    <span class = "form-error">
                      @if($errors->has('description')) 
                        {{ $errors->first('description') }} 
                      @endif
                    </span>
                </div>
                
                <input type="hidden" name="is_active" value="yes"/>
                <input type="hidden" name="is_featured" value="yes"/>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
                {{ Form::token() }}
              </form>
	    </div>
	</div>
@stop

@section('download-manager-scripts')
		
	<script type="text/javascript">
    	document.getElementById("add_category_tab").setAttribute("class", "active");
    </script>
    
@stop