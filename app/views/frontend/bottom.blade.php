@define $blocks=BlocksModel::orderBy('order_index')->get()
@define $image_src = Config::get('app.url') . 'app/modules/blocks/asset/images/'

<?php
function showImage($image)
{
  
  echo ' <img src="' .$image . '" class="img-responsive" />';
}

function showContent($item)
{
  echo '<div>
          <h3>'.$item['title'].'</h3>'.
          '<p>'.$item['information'].'</p>'.
        '</div>';
}
?>


@foreach($blocks as $item)
  @if($item['is_active'] == 'yes')

<div class="col-sm-4">
    <div class="feature-box fbox-plain">
        <div class="fbox-icon bounceIn animated" data-animate="bounceIn">
            {{ showImage($image_src.$item['icon'])}}
        </div>            
        {{ showContent($item) }}
    </div>
</div>

  @endif
  
@endforeach