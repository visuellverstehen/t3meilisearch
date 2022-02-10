<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in('.')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'concat_space' => [
            'spacing' => 'one'
        ],
        'single_blank_line_before_namespace' => true,
        'blank_line_after_namespace' => true,
        'ordered_imports' => true,
        'blank_line_after_opening_tag' => true,
        'declare_equal_normalize' => [
            'space' => 'single'
        ],
        'cast_spaces' => [
            'space' => 'single'
        ],
        'not_operator_with_successor_space' => true,
        'no_unused_imports' => true,
        'trailing_comma_in_multiline' => true,
        'phpdoc_separation' => true,
    ])
    ->setFinder($finder);
