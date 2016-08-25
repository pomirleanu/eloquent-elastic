<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 2:17 PM
 */

namespace EloquentElastic\Console;

use EloquentElastic\Manager as IndexManager;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class DeleteIndex extends Command
{

    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:delete-index
                            {--I|index= : The name of the index to delete}
                            {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an Elasticsearch index';

    /**
     * Index manager instance used for all index operations.
     *
     * @var \EloquentElastic\Manager
     */
    protected $indexManager;


    /**
     * Create a new command instance.
     *
     * @param  \EloquentElastic\Manager $indexManager
     */
    public function __construct(IndexManager $indexManager)
    {
        parent::__construct();
        $this->indexManager = $indexManager;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ( ! $this->confirmToProceed()) {
            return 1;
        }
        $indexName = $this->option('index') ?: $this->indexManager->getDefaultIndex();
        $this->indexManager->deleteIndex($indexName);
        $this->info("Index '{$indexName}' successfully deleted.");
    }
}