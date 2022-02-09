<?php

namespace VV\T3meilisearch\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
// use TYPO3\CMS\Core\Context\Context;
// use TYPO3\CMS\Core\Database\Connection;
// use TYPO3\CMS\Core\Database\ConnectionPool;
// use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
// use TYPO3\CMS\Core\Domain\Repository\PageRepository;
// use TYPO3\CMS\Core\Exception\Page\RootLineException;
// use TYPO3\CMS\Core\Exception\SiteNotFoundException;
// use TYPO3\CMS\Core\Html\HtmlParser;
// use TYPO3\CMS\Core\Site\SiteFinder;
// use TYPO3\CMS\Core\Type\File\ImageInfo;
// use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
// use TYPO3\CMS\Core\Utility\MathUtility;
// use TYPO3\CMS\Core\Utility\PathUtility;
// use TYPO3\CMS\Core\Utility\RootlineUtility;
// use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
// use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
// use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
// use TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository;
// use TYPO3\CMS\IndexedSearch\Lexer;
// use TYPO3\CMS\IndexedSearch\Utility\IndexedSearchUtility;
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
        $query = $this->request->getQueryParams()['query'] ?? '';

        $results = $this->searchService->search($query);

        $this->view->assignMultiple(compact(
            'settings',
            'query',
            'results',
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
