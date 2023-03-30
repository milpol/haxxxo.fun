<?php

namespace Handlers;

use Misc\Http;
use Repositories\ResultsRepository;
use Stooge\Handler;
use Stooge\Request;
use Stooge\Response;

class TasksResultsGetHandler implements Handler
{
    private $resultsRepository;

    public function __construct(ResultsRepository $resultsRepository)
    {
        $this->resultsRepository = $resultsRepository;
    }

    function handle(Request $request, Response $response)
    {
        $results = array();
        for ($i = 1; $i < 7; ++$i) {
            $results['task' . $i] = $this->resultsRepository->getTasksResults($i);
        }
        $response
            ->setJsonEntity($results)
            ->setStatusCode(Http::OK);
    }
}