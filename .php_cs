<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('data')
    ->in(__DIR__ . '/BEAR');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true
    ])
    ->setFinder($finder)
;
