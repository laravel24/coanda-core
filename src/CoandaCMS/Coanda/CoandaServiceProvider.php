<?php namespace CoandaCMS\Coanda;

use Illuminate\Support\ServiceProvider;

class CoandaServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('dover/coanda');

		// Let the main class load any modules prior to handling the bindings
		$this->app->make('coanda')->loadModules();

		// Let the main class handles the bindings
		$this->app->make('coanda')->bindings($this->app);

		// Add any filters
		$this->app->make('coanda')->filters();

		// Add the routes
		$this->app->make('coanda')->routes();

		// Boot up coanda...
		$this->app->make('coanda')->boot($this->app);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{		
		// Bind our main facade
		$this->app->singleton('coanda', function () {

			return new Coanda;

		});

		// $this->app['coanda.setup'] = $this->app->share(function($app)
		// {
		//     return new \CoandaCMS\Coanda\Commands\SetupCommand($app);
		// });
		// $this->commands('coanda.setup');

		$this->app['coanda.delayedpublish'] = $this->app->share(function($app)
		{
		    return new \CoandaCMS\Coanda\Commands\DelayedPublishCommand($app);
		});

		$this->commands('coanda.delayedpublish');
		
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
