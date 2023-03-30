<?php

namespace Handlers;

use Misc\Http;
use Repositories\ResultsRepository;
use Stooge\Handler;
use Stooge\Request;
use Stooge\Response;

class AllResultsGetHandler implements Handler
{
    private $resultsRepository;

    public function __construct(ResultsRepository $resultsRepository)
    {
        $this->resultsRepository = $resultsRepository;
    }

    function handle(Request $request, Response $response)
    {
        $results = $this->resultsRepository->getAllResults();
        $response
            ->setJsonEntity($results)
            ->setStatusCode(Http::OK);
    }
}