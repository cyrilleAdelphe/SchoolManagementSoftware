<?php $categories  = GalleryCategory::where('is_active', 'yes')
			->select('id', 'title', 'description')
			->get();

		foreach ($categories as $category)
		{
			try
			{
				$category->image = 
					Config::get('app.url') .
					'app/modules/gallery/assets/images/thumbnails/' .
					
					Gallery::where('category_id', $category->id)
						->orderBy(DB::raw('RAND()'))
						->select('id')
						->firstOrFail()
						->id;
			}
			catch (Exception $e)
			{
				$category->image = Config::get('app.url') .
					'app/modules/student/assets/images/no-img.png';
			}
		}
?>

<div class = "container">
	<div class="row">
    
		  <div class="col-sm-7 col-md-8 lead-article">
		  	<div class="galTitle">Our Gallery</div>
			<div class="row">
				@foreach ($categories as $category)
					<!-- category starts -->
					<div class="col-sm-4 catBox">
						<div class="galCat">
							<a href="{{ URL::route('gallery-show-category', $category->id) }}">
								<div class="catImg">
									<img class="img-responsive" src="{{ $category->image }}" alt="title here" />
								</div>
								<div class="catTitle">
									{{ HelperController::limitWordCount($category->title, 27) }}
								</div>
							</a>
						</div>
					</div>
					<!-- category ends -->
				@endforeach

			</div>
		</div>
	</div>
</div>