<?php

return [
    'frontend' => [
        'visuellverstehen/t3meilisearch/index' => [
            'target' => \VV\T3meilisearch\Middleware\IndexContent::class,
            'after' => [
                'typo3/cms-frontend/maintenance-mode',
            ],
        ],
    ],
];
