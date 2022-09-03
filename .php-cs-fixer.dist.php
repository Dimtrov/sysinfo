<?php

/**
 * This file is part of dimtrov/sysinfo (PHP System Informations).
 *
 * (c) 2022 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BlitzPHP\CodingStandard\Blitz;
use Nexus\CsConfig\Factory;
use Nexus\CsConfig\Fixer\Comment\NoCodeSeparatorCommentFixer;
use Nexus\CsConfig\Fixer\Comment\SpaceAfterCommentStartFixer;
use Nexus\CsConfig\FixerGenerator;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__ . '/src',
    ])
    ->notName('#Foobar.php$#')
    ->append([
        __FILE__,
    ]);

$overrides = [];

$options = [
    'cacheFile'    => 'build/.php-cs-fixer.cache',
    'finder'       => $finder,
    'customFixers' => FixerGenerator::create('vendor/nexusphp/cs-config/src/Fixer', 'Nexus\\CsConfig\\Fixer'),
    'customRules'  => [
        NoCodeSeparatorCommentFixer::name() => true,
        SpaceAfterCommentStartFixer::name() => true,
    ],
];

return Factory::create(new Blitz(), $overrides, $options)->forLibrary(
    'dimtrov/sysinfo (PHP System Informations)',
    'Dimitri Sitchet Tomkeu',
    'devcode.dst@gmail.com',
    2022
);
