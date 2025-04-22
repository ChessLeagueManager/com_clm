<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()->in(__DIR__);

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'no_trailing_whitespace' => true,
    ])
    ->setFinder($finder);
