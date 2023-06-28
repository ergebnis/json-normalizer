<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

use Ergebnis\License;
use Ergebnis\PhpCsFixer;

$license = License\Type\MIT::markdown(
    __DIR__ . '/LICENSE.md',
    License\Range::since(
        License\Year::fromString('2018'),
        new \DateTimeZone('UTC'),
    ),
    License\Holder::fromString('Andreas Möller'),
    License\Url::fromString('https://github.com/ergebnis/json-normalizer'),
);

$license->save();

$config = PhpCsFixer\Config\Factory::fromRuleSet(new PhpCsFixer\Config\RuleSet\Php81($license->header()), [
    'mb_str_functions' => false,
    'no_unset_on_property' => false,
    'static_lambda' => false,
]);

$config->getFinder()
    ->exclude([
        '.build/',
        '.github/',
        '.note/',
    ])
    ->ignoreDotFiles(false)
    ->in(__DIR__);

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');

return $config;
