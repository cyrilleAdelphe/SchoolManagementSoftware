@define $app_data = ArticlesHelper::getAppData();
<form method = "post" action = "{{URL::route('articles-app-data-create-post')}}">
	<div class="form-group">
	  <label> About Us </label>
	  <textarea name="about_us" placeholder="About Us" 
	  	style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ $app_data['about_us'] }}</textarea>
	</div>

	<div class="form-group">
	  <label> Facebook Link </label>
	  <input name="facebook_link" class="form-control" type="text" value="{{ $app_data['facebook_link'] }}" />
	</div>

	<div class="form-group">
	  <label> Twitter Link </label>
	  <input name="twitter_link" class="form-control" type="text" value="{{ $app_data['twitter_link'] }}" />
	</div>

	<div class="form-group">
	  <label> School Latitude </label>
	  <input name="lat" class="form-control" type="text" value="{{ $app_data['lat'] }}" />
	</div>

	<div class="form-group">
	  <label> School Longitude </label>
	  <input name="lng" class="form-control" type="text" value="{{ $app_data['lng'] }}" />
	</div>

	<div class="form-group">
	  <label> Phone Number </label>
	  <input name="phone_number" class="form-control" type="text" value="{{ $app_data['phone_number'] }}" />
	</div>

	<div class="form-group">
	  <label> Address </label>
	  <input name="address" class="form-control" type="text" value="{{ $app_data['address'] }}" />
	</div>

	<div class="form-group">
	  <label> Email </label>
	  <input name="email" class="form-control" type="text" value="{{ $app_data['email'] }}" />
	</div>

	<div class="form-group">
		<br/>
		<button class="btn btn-primary btn-flat btn-lg" type="submit">Submit</button>
	</div>
	{{ Form::token() }}
</form>