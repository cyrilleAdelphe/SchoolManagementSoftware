@extends('frontend.main')

  @section('content')
  @include('frontend.header-with-slider')
  @include('frontend.slider')

   <section id="content">
        <div class="content-wrap">
            <div class="container clearfix">
                <div id="section-features" class="heading-block title-center page-section">
                    <h2>Why choose us ?</h2>
                    <span>We provide quality education with overall development of our students.</span>
                </div>
                @include('frontend.bottom')
            </div>
        </div>
    </section>
    @include('frontend.notices')
    <section id="content">
        <div class="content-wrap">
            <div class="container clearfix">
                
                <div class="col-sm-7">
                  <div class="row">
                    <div class="col-sm-3">
                      <img src="{{asset('sms/assets/img/principal.jpg')}}" class="img-responsive" />
                    </div>
                    <div class="col-sm-9">
                      <?php 
                        $featured_articles = Articles::where('is_featured', 'yes')->get();
                        $config = json_decode(File::get(app_path() . '/modules/articles/configcategory.json'),true);
                        $word_count =  $config['max_words'];
                        ?>
                        @foreach($featured_articles as $article)
                          <div class="frontBox">
                              <div style="color: #36c2ae; font-size: 25px; line-height: 30px">{{ $article->title }}</div>
                              @define $user = Users::where('username',$article->created_by)->first()
                              @if($user)
                                Posted By: <a href="{{URL::route('users-view-profile',$user['id']) }}">{{$user['name']}}</a>
                              @endif
                            
                            @define $img = ArticlesHelper::getImageLocation($article)
                            @if(strlen($img))
                              <img src = "{{$img}}" class = "img-responsive lead-img" alt = "{{$article->title}}" width=150 height="auto"/>
                            @endif
                            {{HelperController::limitWordCount(preg_replace('/<img.*?>/', '', $article->content),$word_count)}}
                            @define $article_menu = Menus::where('article_id',$article->id)->where('is_active','yes')->get()

                              @define $article_link =  ($article_menu->count()) ? URL::route('menu-view', array(Menus::where('article_id',$article->id)->first()['id'])) : URL::route('menu-view', array(Menus::where('article_id',$article->id)->first()['id']))
                            {{--<a href="{{ $article_link }}" class="btn btn-success btn-flat" >
                                read more
                              </a>--}}
                          </div>
                        @endforeach
                    </div>
                  </div>
                </div>
                <div class="col-sm-5">
                  @include('frontend.upcoming-events')
                </div>
            </div>
        </div>
    </section>

@stop 