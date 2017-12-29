<?php

class MenuHelper
{
 	public function showMenu()
 	{
 		$this->recursiveList();
 	}

 	public function recursiveList($parent_id=null)//null is the alias for root
 	{
 		//if no children, get outta there
 		if (Menus::where('parent_id',$parent_id)->get()->isEmpty())
 		{
 			return;
 		}
 		
 		if($parent_id == null)
 		{
 			echo '<ul class="nav navbar-nav main-link">';	
 			
 			$menu_url = URL::route('home-frontend');
 			echo (Request::url() == $menu_url) ? '<li class="active">' : '<li>';
 			
 			echo '<a href = ' . '"'. $menu_url . '">';
			echo 'HOME';
			echo '</a>';
			echo '</li>';

			

 		}
 		else
 		{
 			echo '<ul class="dropdown-menu">';
 		}

 		$menus = Menus::where('parent_id',$parent_id)
								->orderBy('order_index')
								->get();

		foreach($menus as $menu)
		{
			//if not active,don't show
	 		if (Menus::select('is_active')->where('id',$menu['id'])->first()['is_active'] == 'no')
	 		{
	 			continue;
	 		}
	 		
	 		if($menu['article_id']==null && $menu['external_link']==null)	
	 		{
	 			//for parent menu
	 			//if no children, get outta there
		 		if (Menus::where('parent_id',$menu['id'])->get()->isEmpty())
		 		{
		 			continue;
		 		}

				echo '<li class="dropdown">';
				echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $menu['title'] . '<b class="caret"></b></a>';
				$this->recursiveList($menu['id']);
				echo '</li>';
	 		}
	 		elseif($menu['article_id'])
	 		{
	 			$alias = Articles::where('id', $menu['article_id'])->pluck('alias');
	 			//for article menu
	 			$menu_url = strlen($alias) == 0 ? URL::route('menu-view',$menu['id']) : URL::route('articles-view-from-alias', $alias);//URL::route('articles-view-get',$menu['article_id']);

		 		echo (Request::url() == $menu_url) ? '<li class="active">' : '<li>';
				
				echo '<a href = ' . '"'. $menu_url . '">';
				echo $menu['title'];
				echo '</a>';
				echo '</li>';
		 	}
		 	elseif($menu['external_link'])
		 	{
		 		echo (Request::url() == $menu['external_link']) ? '<li class="active">' : '<li>';
		 		echo '<a href= "'. $menu['external_link'] . '">';
		 		echo $menu['title'];
				echo '</a>';
		 		echo '</li>';
		 	}
		}
 		
 		echo '</ul>';
 	}

 	// public function recursiveList($parent_id=null)//null is the alias for root
 	// {
 	// 	//if no children, get outta there
 	// 	if (Menus::where('parent_id',$parent_id)->get()->isEmpty())
 	// 	{
 	// 		return;
 	// 	}
 		
 	// 	if($parent_id == null)
 	// 	{
 	// 		echo '<ul class="nav navbar-nav">';	
 			
 	// 		$menu_url = URL::route('home-frontend');
 	// 		echo (Request::url() == $menu_url) ? '<li class="active">' : '<li>';
 			
 	// 		echo '<a href = ' . '"'. $menu_url . '">';
		// 	echo 'HOME';
		// 	echo '</a>';
		// 	echo '</li>';

			

 	// 	}
 	// 	else
 	// 	{
 	// 		echo '<ul class="dropdown-menu">';
 	// 	}
 		


 	// 	$parent_menus = Menus::where('parent_id',$parent_id)
		// 						->where('article_id',null)
		// 						->orderBy('order_index')
		// 						->get();
 		
		// foreach($parent_menus as $parent_menu)
		// {

		// 	//if not active,don't show
	 // 		if (Menus::select('is_active')->where('id',$parent_menu['id'])->first()['is_active'] == 'no')
	 // 		{
	 // 			continue;
	 // 		}

	 // 		//if no children, get outta there
	 // 		if (Menus::where('parent_id',$parent_menu['id'])->get()->isEmpty())
	 // 		{
	 // 			continue;
	 // 		}

		// 	echo '<li class="dropdown">';
		// 	echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $parent_menu['title'] . '<b class="caret"></b></a>';
		// 	$this->recursiveList($parent_menu['id']);
		// 	echo '</li>';
		// }


		// //for menus that are articles
		// $article_menus = Menus::where('parent_id',$parent_id)
		// 						->where('article_id','!=',0)
		// 						->orderBy('order_index')
		// 						->get();

		// foreach($article_menus as $article_menu)
		// {
		// 	//if not active,don't show
		// 	if (Menus::select('is_active')->where('id',$article_menu['id'])->first()['is_active'] == 'no')
	 // 		{

	 // 			continue;
	 			
	 // 		}
	 // 		$menu_url = URL::route('articles-view-get',$article_menu['article_id']);

	 // 		echo (Request::url() == $menu_url) ? '<li class="active">' : '<li>';

			
		// 	echo '<a href = ' . '"'. $menu_url . '">';
		// 	echo $article_menu['title'];
		// 	echo '</a>';
		// 	echo '</li>';
		// }

		// echo '</ul>';
 	// }

 	
		
}