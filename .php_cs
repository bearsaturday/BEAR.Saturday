<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
->exclude('vendors')
->exclude('data')
->in(__DIR__ . '/BEAR');

$config = Symfony\CS\Config\Config::create()
->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
->finder($finder);
return $config;
