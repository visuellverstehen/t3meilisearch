<?php

declare(strict_types = 1);

defined('TYPO3') or defined('TYPO3_MODE') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'T3meilisearch',
    'Pi1',
    'LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi1',
    'content-special-indexed_search',
    'LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi1.description'
);

ExtensionUtility::registerPlugin(
    'T3meilisearch',
    'Pi2',
    'LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi2',
    'content-special-indexed_search',
    'LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi2.description'
);
