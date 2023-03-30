<?php

namespace Handlers;

use Misc\Http;
use Stooge\Handler;
use Stooge\Request;
use Stooge\Response;

class IndexHandler implements Handler
{
    function handle(Request $request, Response $response)
    {
        $response
            ->setEntity('General Kenobi!')
            ->setStatusCode(Http::OK);
    }
}