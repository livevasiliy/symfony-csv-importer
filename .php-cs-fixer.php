<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.3.2|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@Symfony:risky' => true,
        '@PhpCsFixer:risky' => true,
        '@PSR12:risky' => true,
        '@PHP74Migration:risky' => true,
        'array_indentation' => true,
        'native_function_invocation' => false,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude([
            'vendor',
        ])
        ->notPath('Kernel.php')
        ->in(__DIR__ . '/src')
    )
    ;
