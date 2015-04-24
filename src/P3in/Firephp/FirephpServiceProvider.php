<?php

namespace P3in\Firephp;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use P3in\Firephp\FirePHPCore\FirePHP;
use P3in\Firephp\FirePHPCore\FB;
use App;
use Config;

class FirephpServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @param  Dispatcher $events
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $this->setupConfig();
        $this->registerEventListeners($events);
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../../config/firephp.php');
        $this->publishes([$source => config_path('firephp.php')]);
        $this->mergeConfigFrom($source, 'firephp');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFirephp();
    }

    /**
     * 
     */
    protected function registerFirephp()
    {
        $this->app->singleton('firephp', function($app) {
            $config = $app['config'];
            $fb = new FB;
            $fb->setEnabled($config['firephp.enabled']);
            return $fb;
        });

        $this->app->alias(
                'firephp', 'P3in\Firephp\Facades\FB'
        );
    }

    /**
     * Register the event listeners.
     *
     * @param  Dispatcher $events
     * @return void
     */
    protected function registerEventListeners(Dispatcher $events)
    {
        if (!App::runningInConsole()) {
            $config = $this->app['config'];
            
            $events->listen('illuminate.log', function($level, $message, $context) {
                $type = 'log';
                $errorInfoKeys = Config::get('firephp::error_keys_and_compact_check');
                switch (strtoupper($level))
                {
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
                    $this->app['firephp']->group($groupLabel);
                    foreach ($errorInfoKeys as $key => $compact_check) {
                        if ($data = $message->{$key}()) {
                            if ($compact_check) {
                                $data = json_decode(json_encode($data));
                            }
                            $this->app['firephp']->{$type}($data, substr($key, 3, strlen($key)));
                        }
                    }
                    $this->app['firephp']->groupEnd();
                } else {
                    $this->app['firephp']->{$type}($message);
                }
            });

            if ($config['firephp.db_profiler']) {
                $events->listen('illuminate.query', function($query, $params, $time, $conn) {
                    $firephp = $this->app['firephp'];
                    $firephp->group("$conn Query");
                    $firephp->log($query, 'Query String');
                    $firephp->log($params, 'Parameters');
                    $firephp->log($time, 'Execution Time');
                    $firephp->groupEnd();
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
