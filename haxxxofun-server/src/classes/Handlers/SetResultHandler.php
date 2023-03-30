<?php

namespace Handlers;

use Misc\Http;
use Repositories\ResultsRepository;
use Stooge\Handler;
use Stooge\Request;
use Stooge\Response;

class SetResultHandler implements Handler
{
    private $resultsRepository;
    private $tasksTokens;

    public function __construct(ResultsRepository $resultsRepository, array $tasksTokens)
    {
        $this->resultsRepository = $resultsRepository;
        $this->tasksTokens = $tasksTokens;
    }

    function handle(Request $request, Response $response)
    {
        $data = $request->getBodyAsArray();
        if ($this->isValid($data) && $this->resultsRepository->isNotDefined($data)) {
            $this->resultsRepository->setResult($data);
            $response->setStatusCode(Http::OK);
        } else {
            $response->setStatusCode(Http::BAD_REQUEST);
        }
    }

    private function isValid($data)
    {
        error_log($data['secret'] === $this->tasksTokens['task' . $data['task']]);
        return isset($data['task']) &&
            isset($this->tasksTokens['task' . $data['task']]) &&
            isset($data['username']) &&
            $this->isInZendeskDomain($data['username']) &&
            isset($data['secret']) &&
            $data['secret'] === $this->tasksTokens['task' . $data['task']];

    }

    private function isInZendeskDomain($haystack)
    {
        return substr_compare($haystack, '@zendesk.com', -strlen('@zendesk.com')) === 0;
    }
}