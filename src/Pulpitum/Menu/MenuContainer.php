<?php namespace Pulpitum\Menu;

/*
|--------------------------------------------------------------------------
| Menu Container
|--------------------------------------------------------------------------
|
| @author Luke Snowden
| @description This IoC containers allows you to inject navigation items
| from anywhere before the render method is called. The output is a simple
| yet flexable navigation structure.
|
*/

class MenuContainer
{

	private $stylesLocation = 'Pulpitum\\Menu\\Styles';

	private $navigations = array();

	private $renders = array();

	/* @name Items
	 * @author Luke Snowden
	 * @param $items (array)
	 * @decription Stores all navigation node arrays
	*/

	private $items = array();

	/*
	 * @method Add Item
	 * @author Luke Snowden
	 * @param $text (string), $url (string), $reference (int), $parent (false/int)
	*/

	public function addItem( $perams = array() )
	{
		$defaults = array(
			'text' 			=> '',
			'URL' 			=> '#',
			'reference' 	=> 0,
			'parent' 		=> false,
			'weight' 		=> 1,
			'class' 		=> '',
			'children'		=> array(),
			'icon'			=> '',
			'attributes'	=> array()
		);
		$this->items[] = array_merge( $defaults, $perams );
		return $this;
	}

	/*
	 * @method To Menu
	 * @author Luke Snowden
	 * @param $name (string)
	*/

	public function toMenu( $name )
	{
		$name = \camel_case( $name );
		if( ! isset( $this->navigations[$name] ) )
		{
			$this->navigations[$name] = new MenuContainerNavigation( $name );
		}
		$this->navigations[$name]->addItem( array_pop( $this->items ) );
	}

	/*
	 * @method Render
	 * @author Luke Snowden
	 * @param $name (false/string)
	*/

	public function render( $name = false )
	{
		if( isset( $this->renders[\camel_case($name)] ) )
		{
			return $this->renders[\camel_case($name)];
		}
		if( ! $name )
		{
			$this->navigations['pmDefaultMenu'] = new MenuContainerNavigation( 'pmDefaultMenu' );
			while( count( $this->items ) !== 0 )
			{
				$item = array_shift( $this->items );
				$this->navigations['pmDefaultMenu']->addItem( $item );
			}
			$this->renders[$name] = '';
			foreach( $this->navigations as $navigation )
			{
				$this->renders[$name] .= $navigation->render();
			}
			return $this->renders[$name];
		}
		else
		{
			$name = \camel_case( $name );
			if( ! isset( $this->navigations[$name] ) )
			{
				// This gets annoying!
				// Throw new \Exception( "Navigation '{$name}' does not exist. Cannot process render." );
				return false;
			}
			$this->renders[$name] = $this->navigations[$name]->render();
			return $this->renders[$name];
		}
	}

	/*
	 * @method Set Menu type
	 * @author Luke Snowden
	 * @param $type (false/string)
	 * @param $menu (false/string)
	*/

	public function setMenuType( $type = false, $menu = false, $location = false, $class=false )
	{
		$menu = \camel_case( $menu );
		if( ! isset( $this->navigations[$menu] ) )
		{
			Throw new \Exception( "Menu '{$menu}' does not exist or you have called this method before the menu has been created." );
		}
		if( $location === false )
		{
			$location = $this->stylesLocation;
		}
		$this->navigations[$menu]->setType( $type, $location, $class );
	}

	public function issetMenu($name){
		$name = \camel_case( $name );
		return isset( $this->navigations[$name] );
	}


}
