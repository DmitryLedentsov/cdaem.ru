<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        sprintf('%s/apps', __DIR__),
        sprintf('%s/common', __DIR__),
        sprintf('%s/environments', __DIR__)
    ]);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'no_empty_phpdoc' => true,
        'single_blank_line_before_namespace' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sortAlgorithm' => 'length'],
        'class_attributes_separation' => [
            'elements' => [
                'const',
                'method',
                'property',
            ]
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'return',
            ]
        ],
    ])
    ->setFinder($finder);