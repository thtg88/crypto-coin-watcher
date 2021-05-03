<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('bootstrap')
    ->exclude('storage')
    ->name('*.stub')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setFinder($finder);
