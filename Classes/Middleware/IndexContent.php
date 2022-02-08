<?php

declare(strict_types=1);

namespace VV\T3meilisearch\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use VV\T3meilisearch\Service\IndexService;
use VV\T3meilisearch\Domain\Model\Document;

class IndexContent implements MiddlewareInterface
{
    protected IndexService $indexService;

    public function __construct()
    {
        $this->indexService = GeneralUtility::makeInstance(IndexService::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // TODO: Check if page should be indexed
        // TODO: Check if indexing is enabled via setting / feature / configuration
        if ($response->getBody()->__toString() !== '' && (int) $GLOBALS['TSFE']->page['no_search'] === 0) {
            $this->indexService->add(Document::createFromRequest($request, $response));
        }

        return $response;
    }
}
