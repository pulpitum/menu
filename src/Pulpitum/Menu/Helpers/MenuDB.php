<?php namespace Pulpitum\Menu\Helpers;

use Menu;
use Sentry;

class MenuDB extends \Eloquent{

	protected $table = 'menus';
	protected $primaryKey = 'id';
	public $timestamps = false;	


	public static function render($name = false, $type="bootstrap", $style="Pulpitum\Auth\Menu", $menu_class="nav navbar-nav"){
		$check = Sentry::check();
		if($name){
			$lang = \Config::get('app.locale');
			$navigation = MenuDB::where( 'menu', '=', $name )->whereIn("language", array("all",$lang))->orderBy("order", "ASC")->get();
			foreach( $navigation as $item )
			{
				//print_r($item->attributes);
				if($item->status==0)
					continue;

				if($item->guest == 0 && $check){
					$permissions = json_decode($item->permissions, 1);
					if($permissions !== NULL && Sentry::getUser()->hasAnyAccess($permissions)){
			    		Menu::addItem( array( 'text' => $item->name, 'URL' => $item->url, 'reference' => $item->id, 'parent' => ($item->parent!=0) ? $item->parent  : false ,'icon'=>$item->icon, 'weight' => $item->order ) )->toMenu( $item->menu );
					}
			    	elseif($permissions==NULL){
			    		Menu::addItem( array( 'text' => $item->name, 'URL' => $item->url, 'reference' => $item->id, 'parent' => ($item->parent!=0) ? $item->parent  : false , 'icon'=>$item->icon, 'weight' => $item->order ) )->toMenu( $item->menu );
			    	}

				}elseif($item->guest == 1 && !$check){
					Menu::addItem( array( 'text' => $item->name, 'URL' => $item->url, 'reference' => $item->id, 'parent' => ($item->parent!=0) ? $item->parent  : false ,'icon'=>$item->icon, 'weight' => $item->order ) )->toMenu( $item->menu );		    	
				}
			}
			try{
				if($type !=="" and $style!=="" and Menu::issetMenu($name)){
					Menu::setMenuType($type, $name, $style, $menu_class);
				}

				return Menu::render( $name );
			}catch(\Exception $e){
				return $e->getMessage()."";
			}
		}

	}


}