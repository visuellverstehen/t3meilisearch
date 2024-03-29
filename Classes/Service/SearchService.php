<?php

namespace VV\T3meilisearch\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use VV\T3meilisearch\Domain\Model\Document;

class SearchService implements SingletonInterface
{
    protected IndexService $indexService;
    protected PropertyMapper $propertyMapper;

    public function __construct(IndexService $indexService, PropertyMapper $propertyMapper)
    {
        $this->indexService = $indexService;
        $this->propertyMapper = $propertyMapper;
    }

    public function search(string $query, string $sorting = 'crdate_desc', array $types = []): ObjectStorage
    {
        $result = new ObjectStorage();
        $typesFilter = [];

        if ($query === '') {
            return $result;
        }

        [$sortingColumn, $sortingDesc] = explode('_', $sorting);

        foreach ($types as $type) {
            $typesFilter[] = 'type = ' . strtolower($type);
        }

        $hits = $this->indexService->search($query, [
            'attributesToCrop' => [
                'content',
            ],
            'attributesToHighlight' => [
                'content',
            ],
            'sort' => [
                $sortingColumn . ':' . strtolower($sortingDesc),
            ],
            // Filter by checking the rootPageId is in the rootline
            'filter' => [
                'rootPageId = ' . $GLOBALS['TSFE']->getSite()->getRootPageId(),
                'languageId IN [-1,' . $GLOBALS['TSFE']->getLanguage()->getLanguageId() . ']',
                $typesFilter,
            ],
        ]);

        if ($hits === null) {
            return $result;
        }

        foreach ($hits->getHits() as $hit) {
            $document = $this->propertyMapper->convert($hit, Document::class);

            $result->attach($document);
        }

        return $result;
    }
}
