<?php

namespace EloquentElastic\Providers;

use EloquentElastic\Client;
use EloquentElastic\Manager;
use Illuminate\Support\ServiceProvider;

class EloquentElasticServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->setupConfig();
        $this->registerElasticsearchClient();
        $this->registerIndexManager();
        $this->registerIndexRepositoryManager();
        $this->registerCommands();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/config/elodex.php');
        $this->publishes([$source => config_path('elodex.php')], 'config');
        $this->mergeConfigFrom($source, 'elodex');
    }
    /**
     * Register the Elasticsearch client manager.
     *
     * @return void
     */
    protected function registerElasticsearchClient()
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = $app['config']->get('elodex.config');
            return new Client($config);
        });
        $this->app->alias(Client::class, 'elodex.client');
    }
    /**
     * Register the index manager.
     *
     * @return void
     */
    protected function registerIndexManager()
    {
        $this->app->singleton(Manager::class, function ($app) {
            $client = $app[Client::class];
            $config = $app['config']->get('elodex');
            return new Manager($client, $config);
        });
        $this->app->alias(Manager::class, 'elodex.index');
    }
    /**
     * Register the index repostiory manager.
     *
     * @return void
     */
    protected function registerIndexRepositoryManager()
    {
        $this->app->singleton(Manager::class, function ($app) {
            $client = $app[Client::class];
            $indexName = $app['config']->get('elodex.default_index', 'default');
            return new Manager($client, $indexName);
        });
        $this->app->alias(Manager::class, 'elodex.repository');
    }
    /**
     * Register the console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $indexManager = $this->app[Manager::class];
        $this->app->singleton(OpenIndex::class, function () use ($indexManager) {
            return new OpenIndex($indexManager);
        });
        $this->app->singleton(CloseIndex::class, function () use ($indexManager) {
            return new CloseIndex($indexManager);
        });
        $this->app->singleton(CreateIndex::class, function () use ($indexManager) {
            return new CreateIndex($indexManager);
        });
        $this->app->singleton(DeleteIndex::class, function () use ($indexManager) {
            return new DeleteIndex($indexManager);
        });
        $this->app->singleton(GetMappings::class, function () use ($indexManager) {
            return new GetMappings($indexManager);
        });
        $this->app->singleton(GetStats::class, function () use ($indexManager) {
            return new GetStats($indexManager);
        });
        $this->app->singleton(GetSettings::class, function () use ($indexManager) {
            return new GetSettings($indexManager);
        });
        $this->app->singleton(Upgrade::class, function () use ($indexManager) {
            return new Upgrade($indexManager);
        });
        $this->app->singleton(Analyze::class, function () use ($indexManager) {
            return new Analyze($indexManager);
        });
        $this->app->singleton(Seed::class, function () use ($indexManager) {
            return new Seed($indexManager);
        });
        $this->commands(
            OpenIndex::class,
            CloseIndex::class,
            CreateIndex::class,
            DeleteIndex::class,
            GetMappings::class,
            GetStats::class,
            GetSettings::class,
            Upgrade::class,
            Analyze::class,
            Seed::class,
            MakeSyncHandler::class
        );
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'elodex.client', ElasticsearchClientManager::class,
            'elodex.index', IndexManager::class,
            'elodex.repository', IndexRepositoryManager::class,
        ];
    }
}
