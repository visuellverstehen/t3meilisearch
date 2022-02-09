<?php

namespace VV\T3meilisearch\Service;

use MeiliSearch\Client;
use MeiliSearch\Search\SearchResult;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use VV\T3meilisearch\Domain\Model\Document;

class IndexService implements SingletonInterface
{
    protected array $settings = [];
    protected Client $client;
    protected string $index = 'pages';

    public function __construct()
    {
        $this->settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3meilisearch');
        $this->client = new Client($this->settings['host'], $this->settings['apiKey']);
        $this->index = $this->settings['index'] ?? 'pages';

        // TODO: Move this into an event listener or command and make
        // settings public configurable
        $this->client->index($this->index)->updateSettings([
            'filterableAttributes' => ['pageUid'],
        ]);
    }

    public function add(Document $document)
    {
        $index = $this->client->index($this->index);
        $index->addDocuments($document->toArray());
    }

    public function search(string $query, array $params = []): SearchResult
    {
        $index = $this->client->index($this->index);

        return $index->search($query, $params);
    }
}
