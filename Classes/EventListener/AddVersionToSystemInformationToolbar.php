<?php

namespace VV\T3meilisearch\EventListener;

use MeiliSearch\Client;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Backend\Toolbar\Enumeration\InformationStatus;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AddVersionToSystemInformationToolbar
{
    public function __invoke(SystemInformationToolbarCollectorEvent $event): void
    {
        $settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3meilisearch');
        $client = new Client($settings['host'], $settings['apiKey']);
        $version = $client->version()['pkgVersion'];

        $event->getToolbarItem()->addSystemInformation(
            'Meilisearch Version',
            $version,
            'actions-search',
            InformationStatus::STATUS_NOTICE
        );
    }
}
