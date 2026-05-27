<?php

declare(strict_types = 1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use VV\T3meilisearch\Controller\SearchController;
use VV\T3meilisearch\Service\IndexService;

defined('TYPO3') or defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached']['t3meilisearch']
    = IndexService::class . '->indexPageContent';

$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'],
    [
        'query',
    ]
);

ExtensionUtility::configurePlugin(
    'T3meilisearch',
    'Pi1',
    [
        SearchController::class => 'search',
    ],
    [
        SearchController::class => 'search',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'T3meilisearch',
    'Pi2',
    [
        SearchController::class => 'form',
    ],
    [
        SearchController::class => '',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
