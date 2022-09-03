# dimtrov/sysinfo (PHP System Informations)
### A lightweight tool to retrieve informations about your PHP environment

[![Coding Standards](https://github.com/Dimtrov/sysinfo/actions/workflows/test-coding-standards.yml/badge.svg)](https://github.com/Dimtrov/sysinfo/actions/workflows/test-coding-standards.yml)
[![PHPStan Static Analysis](https://github.com/Dimtrov/sysinfo/actions/workflows/test-phpstan.yml/badge.svg)](https://github.com/Dimtrov/sysinfo/actions/workflows/test-phpstan.yml)
[![PHPStan level](https://img.shields.io/badge/PHPStan-max%20level-brightgreen)](phpstan.neon.dist)
[![Coverage Status](https://coveralls.io/repos/github/Dimtrov/sysinfo/badge.svg?branch=main)](https://coveralls.io/github/Dimtrov/sysinfo?branch=main)
[![Latest Stable Version](http://poser.pugx.org/Dimtrov/sysinfo/v)](https://packagist.org/packages/Dimtrov/sysinfo)
[![License](https://img.shields.io/github/license/Dimtrov/sysinfo)](LICENSE)
[![Total Downloads](http://poser.pugx.org/Dimtrov/sysinfo/downloads)](https://packagist.org/packages/Dimtrov/sysinfo)

`dimtrov/sysinfo` is a simple library to get some info, metrics and available resources of the system the PHP code is running on.

Highlights
-------

* Simple API
* Framework-agnostic
* Composer ready, [PSR-2] and [PSR-4] compliant

System Requirements
-------

**PHP >= 7.4** 

This library use some native PHP functions like `shell_exec`, `php_uname`, `disk_total_space`, `disk_free_space`, `memory_get_usage`, `memory_get_peak_usage` which may be disabled by some shared hostings.


## Installation

```bash
# Install the package
composer require dimtrov/sysinfo
```

## Usage

``` php
use Dimtrov\Systinfo;

$sysinfo = new Systinfo();


$sysinfo->cpuArchitecture();  // Intel x64
$sysinfo->cpuCores();  // 2
$sysinfo->cpuFree();  // 
$sysinfo->cpuFrequency();  // 2.90GHz
$sysinfo->cpuName();  // Intel(R) Core(TM) i7-3520M CPU @ 2.90GHz
$sysinfo->cpuProcessors();  // 4
$sysinfo->cpuSpeed();  // 1.11 GHz
$sysinfo->cpuVendor();  // Intel

$sysinfo->diskCapacity();  // 998.87 GB
$sysinfo->diskCountPartitions(); 3
$sysinfo->diskFree(); // 310 G
$sysinfo->diskPartitions(); // [C:\, D:\, E:\]
$sysinfo->diskPartitionsSpaces(); // ["C:\" => "440 GB", "D:\" => "244 GB", "E:\" => "244GB"]
$sysinfo->diskTotal(); // 440GB
$sysinfo->diskUsed(); // 190GB
$sysinfo->diskUsedPercentage(); // 25%

$sysinfo->executionTimeLimit(); // 60s
$sysinfo->hostname(); // dimtrovich
$sysinfo->kernel(); // 
$sysinfo->memoryLimit(); // 8 MB
$sysinfo->memoryUsage(); // 2.3 MB
$sysinfo->os(); // Windows
$sysinfo->osRelease(); // Microsoft Windows 11 Pro

$sysinfo->ramCount(); // 2
$sysinfo->ramFree();  // 1.3 GB
$sysinfo->ramList(); // [4GB, 4GB]
$sysinfo->ramTotal(); // 8GB
$sysinfo->ramUsedPercentage(); // 91%
```

## Warning
To date (02/09/22), this class has only been tested on Windows. Implementations on Linux and Mac are not yet fully completed and therefore have not been tested. Your pull requests are welcome

## API

``` php
/**
 * Count number of cpu cores
 */
public function cpuCores(): int;

/**
 * Retrieve a free cpu usage
 */
public function cpuFree();

/**
 * Retrieve a real frequency of processor
 *
 * @return int|string if $format set to true, return a string like `'2GHz'` otherwise, return a raw value in Hz like `2000000`
 */
public function cpuFrequency(bool $format = true);

/**
 * Retrieve cpu name
 */
public function cpuName(): string;

/**
 * Count number of logical processors
 */
public function cpuProcessors(): int;

/**
 * Retrieves the current rotation speed of the processor
 *
 * @return int|string if $format set to true, return a string like `'2GHz'` otherwise, return a raw value in Hz like `2000000`
 */
public function cpuSpeed(bool $format = true);

/**
 * Get cpu manufacturer
 */
public function cpuVendor(): string;

/**
 * Collect all the partitions of the hard drive
 */
public function diskPartitions(): array;

/**
 * Retrieve the free ram resources.
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function ramFree(bool $format = true);

/**
 * List the different memory bar
 * 
 * @return array<int|string> * @return int|string if $format set to true, return an array of string like `['2GB', '4GB']` otherwise, return an array of raw value in bytes like `[2000000, 40000]`
 */
public function ramList(bool $format = true): array;

/**
 * Get the cpu architecture and bits
 */
public function cpuArchitecture(): string;

/**
 * Recovering the total storage capacity of the hard drive
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function diskCapacity(bool $format = true);

/**
 * Counts the number of hard disk partitions
 */
public function diskCountPartitions(): int;

/**
 * Retrieves the free storage space of a given partition of the hard drive
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function diskFree(bool $format = true, string $partition = '/');

/**
 * Determine the storage capacity of each partition on the hard drive
 * 
 * @return array<int|string> if $format set to true, return an array of string like `['2GB', '4GB']` otherwise, return an array of raw value in bytes like `[2000000, 40000]`
 */
public function diskPartitionsSpaces(bool $format = true): array;

/**
 * Recovering the total storage capacity of a given partition of the hard drive
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function diskTotal(bool $format = true, string $partition = '/');

/**
 * Retrieve the used resources from the total resources as a percentage.
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function diskUsed(bool $format = true, string $partition = '/');

/**
 * Retrieve the used resources from the total resources as a percentage.
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function diskUsedPercentage(bool $format = true, string $partition = '/');

/**
 * Finds out max PHP execution time limit from php.ini
 *
 * @return int in seconds. If set to zero, no time limit is imposed.
 */
public function executionTimeLimit(): int;

/**
 * Get the OS hostname
 */
public function hostname(): string;

/**
 * Get the OS kernel
 */
public function kernel(): string;

/**
* Finds out PHP memory limit from php.ini
*
* @return int|string if $format set to true, return a string like `'2MB'` otherwise, return a raw value in bytes like `2000`
*/
public function memoryLimit(bool $format = true);

/**
 * Returns the amount of memory allocated to PHP
 *
 * @return int|string if $format set to true, return a string like `'2MB'` otherwise, return a raw value in bytes like `2000`
*/
public function memoryUsage(bool $format = true);

/**
 * Get the Os
 */
public function os(): string;

/**
 * Get the Os release
 */
public function osRelease(): string;

/**
 * Count the number of memory bars available
 */
public function ramCount(): int;

/**
 * Retrieve the total ram resources.
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function ramTotal(bool $format = true);

/**
 * Get the consumption rate (as a percentage) of the RAM
 *
 * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
 */
public function ramUsedPercentage(bool $format = true);
```

## Credits

- [Dimitri Sitchet Tomkeu](https://github.com/dimtrovich)

## Contributing

Thank you for considering contributing to this package! Please create a pull request with your contributions with detailed explanation of the changes you are proposing.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
