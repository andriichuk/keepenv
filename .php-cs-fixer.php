<?php

declare(strict_types=1);

$finder = \PhpCsFixer\Finder::create()
    ->in(['src', 'tests']);

$config = new \PhpCsFixer\Config();

return $config->setRules([
    '@PSR1' => true,
    '@PSR2' => true,
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
])->setFinder($finder);


