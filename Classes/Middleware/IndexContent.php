<?php

declare(strict_types=1);

namespace VV\T3meilisearch\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IndexContent implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response->getBody()->__toString() !== '') {
            // TODO: Parse content and save to index
        }

        return $response;
    }
}
