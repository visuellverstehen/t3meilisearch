<?php

namespace VV\T3meilisearch\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use VV\T3meilisearch\Service\SearchService;

class SearchController extends ActionController
{
    protected SearchService $searchService;

    public function injectSearchService(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function searchAction(): ResponseInterface
    {
        $settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3meilisearch');
        $queryParams = $this->request->getQueryParams();
        $query = $queryParams['query'] ?? '';
        $sorting = $queryParams['sorting'] ?? 'crdate_desc';
        $types = $queryParams['types'] ?? [];

        // TODO: Maybe create a SearchRequestDTO
        $results = $this->searchService->search($query, $sorting, $types);

        $this->view->assignMultiple(compact(
            'settings',
            'query',
            'results',
            'queryParams',
        ));

        return $this->htmlResponse();
    }

    public function formAction(): ResponseInterface
    {
        $settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3meilisearch');

        $this->view->assignMultiple(compact(
            'settings',
        ));

        return $this->htmlResponse();
    }
}
