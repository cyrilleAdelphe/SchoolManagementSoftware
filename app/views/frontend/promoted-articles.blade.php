<div id="carousel-top" class="carousel slide" data-ride="carousel">
  <div class="carousel-inner">
  	@define $promoted = Articles::where('is_featured','yes')->orderBy('created_at','DESC')->get()
    <ol class="carousel-indicators">
    	@for($i=0;$i<$promoted->count();$i++)
    		<li data-target="#carousel-top" data-slide-to="{{$i}}" class="{{!$i?'active':''}}"></li>	
    	@endfor
    </ol>    
    @for($i=0;$i<$promoted->count();$i++)
      <div class="item {{!$i?'active':''}}">
        <div class="promotedArticle">
          @define $img_src = (File::exists(app_path() . '/modules/articles/asset/intro image/' . $promoted[$i]['id'] . '.jpg'))                     ? Config::get('app.url').'app/modules/articles/asset/intro image/'.$promoted[$i]['id'].'.jpg': asset('sms/assets/img/no_image.jpg');
          <img style="max-height:220px" src="{{$img_src}}" alt="Promoted Image" class="img-responsive" />
          <h4 class="mainTitle">{{$promoted[$i]['title']}}</h4>
          @define $config = json_decode(File::get(app_path() . '/modules/articles/configcategory.json'),true);
          @define $word_count =  $config['max_words'];
          {{strip_tags(HelperController::limitWordCount(preg_replace('/<img.*?>/', '', $promoted[$i]['content']), $word_count))}}
          <div class="clear"></div>
          <a class="btn btn-success btn-xs" href="{{ArticlesHelper::getArticleLink($promoted[$i])}}">
            	Read more..
          </a>
          
        </div>
      </div>
    @endfor 
    <a class="left carousel-control" href="#carousel-top" data-slide="prev">
      <span class="fa fa-angle-left"></span>
    </a>
    <a class="right carousel-control" href="#carousel-top" data-slide="next">
      <span class="fa fa-angle-right"></span>
    </a>             
  </div><!-- carousel inner ends -->

</div><!-- carousel ends -->