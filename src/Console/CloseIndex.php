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

class CloseIndex extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:close-index
                            {--I|index= : The name of the opened index to close}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close an opened index';

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
     *
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
        $indexName = $this->option('index') ?: $this->indexManager->getDefaultIndex();
        $this->indexManager->closeIndex($indexName);
        $this->info("Index '{$indexName}' successfully closed.");
    }
}