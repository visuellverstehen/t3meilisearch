<?php

declare(strict_types = 1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use VV\T3meilisearch\Controller\SearchController;
use VV\T3meilisearch\Service\IndexService;

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached']['t3meilisearch']
    = IndexService::class . '->indexPageContent';

ExtensionUtility::configurePlugin(
    'T3meilisearch',
    'Pi1',
    [
        SearchController::class => 'search',
    ],
    [
        SearchController::class => 'search',
    ],
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
);

// Show core checkbox to exclude pages from indexing
ExtensionManagementUtility::addPageTSConfig('
    TCEFORM.pages.no_search.disabled = 0
');

// Add content elements for plugins
ExtensionManagementUtility::addPageTSConfig('
    mod.wizards.newContentElement.wizardItems.forms {
        elements {
            search {
                iconIdentifier = content-special-indexed_search
                title = LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi1
                description = LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi1.description
                tt_content_defValues {
                    CType = list
                    list_type = t3meilisearch_pi1
                }
                saveAndClose = true
            }
            mini_search {
                iconIdentifier = content-special-indexed_search
                title = LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi2
                description = LLL:EXT:t3meilisearch/Resources/Private/Language/locallang.xlf:pi2.description
                tt_content_defValues {
                    CType = list
                    list_type = t3meilisearch_pi1
                }
                saveAndClose = true
            }
        }
        show :=addToList(search)
        show :=addToList(mini_search)
    }
');
