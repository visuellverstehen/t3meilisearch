<?php

namespace VV\T3meilisearch\Service;

use MeiliSearch\Search\SearchResult;
use TYPO3\CMS\Core\SingletonInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use VV\T3meilisearch\Domain\Model\Document;

class SearchService implements SingletonInterface
{
    protected IndexService $indexService;
    protected PropertyMapper $propertyMapper;
    protected array $treeList = [];

    public function __construct(IndexService $indexService, PropertyMapper $propertyMapper)
    {
        $this->indexService = $indexService;
        $this->propertyMapper = $propertyMapper;

        $rootPageId = $GLOBALS['TSFE']->getSite()->getRootPageId();
        $treeList = $GLOBALS['TSFE']->cObj->getTreeList($rootPageId, 5);
        $this->treeList = explode(',', $treeList);
    }

    public function search(string $query): ObjectStorage
    {
        $result = new ObjectStorage();

        if ($query === '') {
            return $result;
        }

        $hits = $this->indexService->search($query, [
            'attributesToCrop' => [
                'content',
            ],
            'attributesToHighlight' => [
                'content',
            ],
            // Filter by checking the pageUid is in the rootline
            'filter' => call_user_func(function() {
                $filters = [];

                foreach($this->treeList as $pageUid) {
                    $filters[] = 'pageUid = ' . $pageUid;
                }

                return implode(' OR ', $filters);
            }),
        ])->getHits();

        foreach($hits as $hit) {
            $document = $this->propertyMapper->convert($hit, Document::class);

            $result->attach($document);
        }

        return $result;
    }
}
