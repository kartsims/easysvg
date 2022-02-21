<?php
declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in('src');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setFinder($finder);
