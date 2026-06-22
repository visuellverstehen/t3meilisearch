<?php

declare(strict_types = 1);

namespace VV\T3meilisearch\EventListener;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;
use VV\T3meilisearch\Domain\Model\Document;
use VV\T3meilisearch\Service\IndexService;

class IndexContent
{
    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        // Only do this when caching is enabled
        if ($event->isCachingEnabled() === false) {
            return;
        }

        $page = $event->getRequest()->getAttribute('frontend.page.information')->getPageRecord();

        if ((int) $page['no_search'] === 1 || (int) $page['no_index'] === 1) {
            return;
        }

        $indexService = GeneralUtility::makeInstance(IndexService::class);

        if ($event->getContent() !== '') {
            $indexService->add(Document::createFromContent((string)$event->getContent()));
        }

        $indexService->checkForFiles($event->getContent());
    }
}
