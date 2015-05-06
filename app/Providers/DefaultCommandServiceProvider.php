<?php namespace App\Providers;

use App\Commands\AppNameCommand;
use App\Commands\CommandMakeCommand;
use App\Commands\ProviderMakeCommand;
use Bkoetsier\BaseConsole\Foundation\Composer;
use Illuminate\Support\ServiceProvider;

class DefaultCommandServiceProvider extends ServiceProvider {


    protected $commands = [
        'AppName' => 'console.fw.app.name',
        'ProviderMake' => 'console.fw.provider.make',
        'ConsoleMake' => 'console.fw.console.make'
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application.
     *
     * @return void
     */
    public function register()
    {
        $this->registerComposerWrapper();
        foreach (array_keys($this->commands) as $command)
        {
            $method = "register{$command}Command";
            call_user_func_array([$this, $method], []);
        }
        $this->commands(array_values($this->commands));
    }

    protected function registerComposerWrapper()
    {
        $this->app->singleton('composer',function($app){
            return new Composer($app['files'],base_path());
        });
    }

    protected function registerAppNameCommand()
    {
        $this->app->singleton('console.fw.app.name', function($app)
        {
            return new AppNameCommand($app['composer'], $app['files']);
        });
    }

    protected function registerProviderMakeCommand()
    {
        $this->app->singleton('console.fw.provider.make', function($app)
        {
            return new ProviderMakeCommand($app['files']);
        });
    }
    protected function registerConsoleMakeCommand()
    {
        $this->app->singleton('console.fw.console.make', function($app)
        {
            return new CommandMakeCommand($app['files']);
        });
    }



}