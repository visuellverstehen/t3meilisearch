<?php

namespace VV\T3meilisearch\Service;

use MeiliSearch\Client;
use MeiliSearch\Search\SearchResult;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Spatie\PdfToText\Pdf;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use VV\T3meilisearch\Domain\Model\Document;

class IndexService implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected array $settings = [];
    protected Client $client;
    protected string $index = 'pages';

    public function __construct()
    {
        $this->settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3meilisearch');
        $this->client = new Client($this->settings['host'], $this->settings['apiKey']);
        $this->index = $this->settings['index'] ?? 'pages';

        if ($this->client->isHealthy()) {
            // TODO: Move this into an event listener or command and make
            // settings public configurable
            $this->client->index($this->index)->updateSettings([
                'filterableAttributes' => ['rootPageId', 'type'],
                'sortableAttributes' => ['crdate'],
            ]);
        }
    }

    public function add(Document $document)
    {
        if ($this->client->isHealthy()) {
            $index = $this->client->index($this->index);
            $index->addDocuments($document->toArray());
        } else {
            $this->logger->warning('MeiliSearch is not healthy. Credentials correct?');
        }
    }

    public function search(string $query, array $params = []): ?SearchResult
    {
        if ($this->client->isHealthy()) {
            $index = $this->client->index($this->index);

            return $index->search($query, $params);
        }

        $this->logger->warning('MeiliSearch is not healthy. Credentials correct?');

        return null;
    }

    public function indexPageContent(array $parameters, TypoScriptFrontendController $tsfe)
    {
        if ((int) $tsfe->page['no_search'] === 1) {
            return;
        }

        if ($tsfe->content !== '') {
            $this->add(Document::createFromTSFE($tsfe));
        }

        $this->checkForFiles($tsfe);
    }

    public function checkForFiles(TypoScriptFrontendController $tsfe)
    {
        // Extract links to PDFs in fileadmin to parse
        preg_match_all('/\/fileadmin(.*?)\.pdf/', $tsfe->content, $links);

        foreach ($links[0] as $link) {
            $absolutePath = Environment::getPublicPath() . $link;

            try {
                $content = Pdf::getText($absolutePath);
            } catch (\Throwable $e) {
                continue;
            }

            $document = new Document();
            $document->setId(md5($link));
            $document->setUrl($link);
            $document->setRootPageId($tsfe->getSite()->getRootPageId() ?? 0);
            $document->setContent($content);
            $document->setType('pdf');
            $document->setCrdate(filemtime($absolutePath));

            $this->add($document);
        }
    }
}
