<?php

declare(strict_types=1);

namespace VV\T3meilisearch\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\AfterCachedPageIsPersistedEvent;
use VV\T3meilisearch\Domain\Model\Document;
use VV\T3meilisearch\Service\IndexService;

class IndexContent
{
    public function __invoke(AfterCachedPageIsPersistedEvent $event): void
    {
        // Only do this when caching is enabled
        if ($event->isCachingEnabled() === false) {
            return;
        }

        $tsfe = $event->getController();

        if ((int) $tsfe->page['no_search'] === 1 || (int) $tsfe->page['no_index'] === 1) {
            return;
        }

        $indexService = GeneralUtility::makeInstance(IndexService::class);

        if ($tsfe->content !== '') {
            $indexService->add(Document::createFromTSFE($tsfe));
        }

        $indexService->checkForFiles($tsfe);
    }
}