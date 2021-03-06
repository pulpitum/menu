<?php namespace Pulpitum\Menu;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider 
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */

	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register()
	{
		$this->app['menu'] = $this->app->share( function( $app )
		{
			return new Menu;
		});
		$this->app['config']->package( "pulpitum/menu", dirname( __FILE__ ) . "/../../../config" );
        $this->app->booting(function () {
          $loader = \Illuminate\Foundation\AliasLoader::getInstance();
          $loader->alias('MenuDB', 'Pulpitum\Menu\Helpers\MenuDB');
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */

	public function provides()
	{
		return array();
	}

}