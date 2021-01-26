<?php

namespace App\Console\Commands;

use App\Services\DataMoviePersonService;
use Illuminate\Console\Command;

class UpdateMovieFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movie:update {nbrLines}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var DataMoviePersonService
     */
    protected $dataMoviePersonService;

    public function __construct(DataMoviePersonService $dataMoviePersonService)
    {
        parent::__construct();

        $this->dataMoviePersonService = $dataMoviePersonService;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $nbrLines = $this->argument('nbrLines');
        $personIds = $this->dataMoviePersonService->findPersonsWithKwmLpNotNUl($nbrLines);
        dump($personIds);
        if(!empty($personIds))
           $this->dataMoviePersonService->generateDataPersons($personIds);

        $this->dataMoviePersonService->deleteProcessedPersonsIds($personIds);

    }
}
