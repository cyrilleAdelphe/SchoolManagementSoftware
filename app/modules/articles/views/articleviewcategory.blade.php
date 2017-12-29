@extends('frontend.main')

@section('content')
  
    
  <h1>{{ $category['title'] }}</h1>

  @if(sizeof($articles))
    @foreach($articles as $data)
      <h3>{{$data->title}}</h3>
      <p>
        @define $user = Users::where('username',$data->created_by)->first()
        @if($user)
          Posted By: <a href="{{URL::route('users-view-profile',$user['id']) }}">{{$user['name']}}</a>
        @endif
      </p>
      <p>
        <!-- <img src="images/articles/accountant.jpg" class="img-responsive lead-img" alt="Chartered Accountant" /> -->
        @define $img = ArticlesHelper::getImageLocation($data)
        @if(strlen($img))
          <img src = "{{$img}}" class = "img-responsive lead-img" alt = "{{$data->title}}" />
        @endif
        {{HelperController::limitWordCount(preg_replace('/<img.*?>/', '', $data->content),$word_count)}}
        @define $article_menu = Menus::where('article_id',$data->id)->where('is_active','yes')->get()
        <p>

          @define $article_link =  ($article_menu->count()) ? URL::route('menu-view', array(Menus::where('article_id',$data->id)->first()['id'])) : URL::route('articles-view-get', $data->id)
          <a href="{{ $article_link }}" class="btn btn-flat btn-success">
            Read more...
          </a>
        </p>
        
        
    @endforeach
    {{$articles->links()}}
  @else
    <h1>No Featured Articles Present</h1>
  @endif
      
 
@stop