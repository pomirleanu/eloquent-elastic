<?php
/**
 * Created by PhpStorm.
 * User: pomir
 * Date: 8/25/2016
 * Time: 2:12 PM
 */

namespace EloquentElastic\Console;

use Illuminate\Console\Command;
use EloquentElastic\Manager as IndexManager;
use Illuminate\Support\Debug\Dumper;

class Analyze extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:analyze
                            {analyzer : Analyzer used to analyze the given text}
                            {text : Text to analyze}
                            {--F|filter= : A comma-separated list of filters to use for the analysis}
                            {--T|tokenizer= : The name of the tokenizer to use for the analysis}
                            {--I|index= : Name of the index}
                            {--dump : Print the result as a dump}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze a given text with the specified analyzer';
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
        $analyzer = $this->argument('analyzer');
        $text = $this->argument('text');
        $indexName = $this->option('index') ?: $this->indexManager->getDefaultIndex();
        $dump = $this->option('dump') ? true : false;
        $filters = $this->option('filter') ? explode(',', $this->option('filter')) : [];
        $tokenizer = $this->option('tokenizer');
        $results = $this->indexManager->analyze($analyzer, $text, $filters, $tokenizer, $indexName);
        if ($dump) {
            (new Dumper)->dump($results);
        } else {
            $this->printTokens($results['tokens']);
        }
    }
    /**
     * Print the tokens.
     *
     * @param  array $tokens
     * @return void
     */
    protected function printTokens(array $tokens)
    {
        $headers = ['Token', 'Start offset', 'End offset', 'Type', 'Position'];
        $this->table($headers, $tokens);
    }
}