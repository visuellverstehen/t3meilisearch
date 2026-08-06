<?php

declare(strict_types = 1);

namespace VV\T3meilisearch\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;
use VV\T3meilisearch\Domain\Model\Document;
use VV\T3meilisearch\Service\IndexService;

#[AsEventListener]
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

        $content = '';
        $version = new Typo3Version();
        $indexService = GeneralUtility::makeInstance(IndexService::class);

        if ($version->getMajorVersion() < 14) {
            // @todo: Remove if() when TYPO3 v13 compatibility is dropped
            $tsfe = $event->getController();
            $content = (string) $tsfe->content;
        } else {
            $content = (string) $event->getContent();
        }

        if ($content !== '') {
            $indexService->add(Document::createFromContent($content));
        }

        $indexService->checkForFiles($content);
    }
}
