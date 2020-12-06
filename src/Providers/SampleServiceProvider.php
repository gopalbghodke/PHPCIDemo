<?php

namespace Brunoocto\Sample\Providers;

use Dotenv\Dotenv;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Brunoocto\Sample\Contracts\SampleInterface;
use Brunoocto\Sample\Models\ProjectManagement\Users;

/*
 * This importation specify the real service used.
 */
use Brunoocto\Sample\Services\SampleService;

class SampleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Load environment variables specific to the library (default is .env)
        $dotenv = Dotenv::createMutable(__DIR__.'/../../');
        $dotenv->load();

        // Add SQLite database by merging configuration file
        $this->mergeConfigFrom(
            __DIR__.'/../../config/database.connections.php',
            'database.connections'
        );

        // To avoid conflict, we can disable the resources load
        if (env('LINCKO_SAMPLE_RESOURCES_ENABLE') && in_array(env('APP_ENV'), ['testing', 'local'])) {
            // (config vnd 1/3) Register all resources to map as VND resource
            $this->mergeConfigFrom(
                __DIR__.'/../../config/json-api.resources.php',
                'json-api-brunoocto-vmodel.resources'
            );
        }

        // (config vnd 2/3) Customized User binding
        $this->app->singleton(Model::class, Users::class);

        // Sample binding
        // The alias make sure that the Facade (\LinckoSample::) and the Maker will work
        $this->app->alias(SampleInterface::class, 'sample_interface');
        // The singleton (or bind) set any specification at instanciation
        $this->app->singleton(SampleInterface::class, SampleService::class);
        $this->app->singleton(SampleService::class, function () {
            $sample = new SampleService;
            // It helps to see in the response if the code has been throw this part (binding at instanciation)
            $sample->bind();
            return $sample;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes into the framework (loadRoutesFrom takes advantage of Laravel cache)
        $this->loadRoutesFrom(__DIR__.'/../Routes/SampleRoutes.php');

        // Generate tables
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // (config vnd 3/3) Link to the Users Class model specific to the API, the default is a virtual (non-persistant) one Brunoocto\Vmodel\Models\VmodelUsers
        config(['auth.providers.vmodel.model' => Users::class]);
    }
}
