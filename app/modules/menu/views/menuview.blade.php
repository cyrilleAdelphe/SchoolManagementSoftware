@define $article = Articles::find($menu['article_id'])

@extends('articles.views.articleview')

@section('page-header')
	<div class="pImg">
		@if(File::exists(app_path() . '/modules/menu/asset/images/' . $menu['id'] . '.jpg'))
			<img class="img-responsive"  src = "{{ Config::get('app.url').'/app/modules/menu/asset/images/'. $menu['id'] .'.jpg'}}" height = "150" width = "1366">
		@else
			<img class="img-responsive" src = "{{asset('sms/assets/img/no_image.jpg')}}">
		@endif
	</div>
@stop