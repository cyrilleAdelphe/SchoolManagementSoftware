@define $image_src = Config::get('app.url').'app/modules/list/asset/images/'
@define $list = ListModel::orderBy('order_index')->get()
<div class="col-xs-12 col-sm-12 col-md-6">
  @foreach($list as $item)
    @if($item['is_active'] == 'yes')
      <div class="row shortDisplay ">
        <div class="col-sm-3 col-md-3 ">
          <img class="img-responsive" src="{{$image_src . $item['icon']}}" alt="Knowledge" />
        </div>
        <div class="col-sm-9 col-md-9 ">
          <p class="lead text-light-blue shortTitle">{{ $item['title'] }}</p>
          <p>{{ $item['information'] }}</p>
        </div>
      </div>
    @endif
  @endforeach
</div>