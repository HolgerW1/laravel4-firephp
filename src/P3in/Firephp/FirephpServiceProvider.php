<?php namespace P3in\Firephp;

use Illuminate\Support\ServiceProvider;
use P3in\Firephp\FirePHPCore\FirePHP;
use P3in\Firephp\FirePHPCore\FB;
use App;
use Config;

class FirephpServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
	var $pack = 'p3in/firephp';
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package($this->pack);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['config']->package($this->pack, __DIR__.'/../../config');

		$this->app['fb'] = $this->app->share(function($app){
			$fb = new FB;
			$fb->setEnabled(Config::get('firephp::enabled'));
			return $fb;
		});

		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('FB', 'P3in\Firephp\Facades\FB');
		});
		if(!App::runningInConsole()){
			$this->app->events->listen('illuminate.log', function($level, $message, $context){
				$type = 'log';
				$errorInfoKeys = Config::get('firephp::error_keys_and_compact_check');
				switch (strtoupper($level)){
					case FirePHP::INFO :
					case FirePHP::WARN :
					case FirePHP::LOG :
					case FirePHP::ERROR :
						$type = $level;
					break;
					case 'WARNING':
						$type = 'warn';
					break;
				}
				if (is_object($message)) {
					$groupLabel = $message->getMessage() ? $message->getMessage() : 'Error';
					$this->app['fb']->group($groupLabel);
						foreach ($errorInfoKeys as $key => $compact_check) {
							if ($data = $message->{$key}()) {
								if ($compact_check) {
									$data = json_decode(json_encode($data));
								}
								$this->app['fb']->{$type}($data,substr($key,3,strlen($key)));
							}
						}
					$this->app['fb']->groupEnd();
				}else{
					$this->app['fb']->{$type}($message);
				}
			});
			if (Config::get('firephp::db_profiler')) {
				$this->app->events->listen('illuminate.query', function($query, $params, $time, $conn){
					$this->app['fb']->group("$conn Query");
						$this->app['fb']->log($query,'Query String');
						$this->app['fb']->log($params,'Parameters');
						$this->app['fb']->log($time,'Execution Time');
					$this->app['fb']->groupEnd();
				});
			}
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('firephp');
	}

}