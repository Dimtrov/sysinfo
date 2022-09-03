<?php

/**
 * This file is part of dimtrov/sysinfo (PHP System Informations).
 *
 * (c) 2022 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrov\Sysinfo\Adapters;

abstract class BaseAdapter
{
    /**
     * Count number of cpu cores
     */
    abstract public function cpuCores(): int;

    /**
     * Retrieve a free cpu usage
     */
    abstract public function cpuFree();

    /**
     * Retrieve a real frequency of processor
     *
     * @return int|string if $format set to true, return a string like `'2GHz'` otherwise, return a raw value in Hz like `2000000`
     */
    abstract public function cpuFrequency(bool $format = true);

    /**
     * Retrieve cpu name
     */
    abstract public function cpuName(): string;

    /**
     * Count number of logical processors
     */
    abstract public function cpuProcessors(): int;

    /**
     * Retrieves the current rotation speed of the processor
     *
     * @return int|string if $format set to true, return a string like `'2GHz'` otherwise, return a raw value in Hz like `2000000`
     */
    abstract public function cpuSpeed(bool $format = true);

    /**
     * Get cpu manufacturer
     */
    abstract public function cpuVendor(): string;

    /**
     * Collect all the partitions of the hard drive
     */
    abstract public function diskPartitions(): array;

    /**
     * Retrieve the free ram resources.
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    abstract public function ramFree(bool $format = true);

    /**
     * List the different memory bar
     * 
     * @return array<int|string> * @return int|string if $format set to true, return an array of string like `['2GB', '4GB']` otherwise, return an array of raw value in bytes like `[2000000, 40000]`
     */
    abstract public function ramList(bool $format = true): array;

    /**
     * Get the cpu architecture and bits
     */
    public function cpuArchitecture(): string
    {
        return php_uname('m');
    }

    /**
     * Recovering the total storage capacity of the hard drive
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    public function diskCapacity(bool $format = true)
    {
        $capacity = array_sum($this->diskPartitionsSpaces(false));

        return $format ? $this->byte2size($capacity) : $capacity;
    }

    /**
     * Counts the number of hard disk partitions
     */
    public function diskCountPartitions(): int
    {
        return count($this->diskPartitions());
    }

    /**
     * Retrieves the free storage space of a given partition of the hard drive
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    public function diskFree(bool $format = true, string $partition = '/')
    {
        $usage = (int) disk_free_space($partition);

        return $format ? $this->byte2size($usage) : $usage;
    }

    /**
     * Determine the storage capacity of each partition on the hard drive
     * 
     * @return array<int|string> if $format set to true, return an array of string like `['2GB', '4GB']` otherwise, return an array of raw value in bytes like `[2000000, 40000]`
     */
    public function diskPartitionsSpaces(bool $format = true): array
    {
        $partitions = $this->diskPartitions();

        foreach ($partitions as $key => $value) {
            $partitions[$value] = $this->diskTotal($format, $value);
            unset($partitions[$key]);
        }

        return $partitions;
    }

    /**
     * Recovering the total storage capacity of a given partition of the hard drive
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    public function diskTotal(bool $format = true, string $partition = '/')
    {
        $usage = (int) disk_total_space($partition);

        return $format ? $this->byte2size($usage) : $usage;
    }

    /**
     * Retrieve the used resources from the total resources as a percentage.
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    public function diskUsed(bool $format = true, string $partition = '/')
    {
        $usage = $this->diskTotal(false, $partition) - $this->diskFree(false, $partition);

        return $format ? $this->byte2size($usage) : $usage;
    }

    /**
     * Retrieve the used resources from the total resources as a percentage.
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    public function diskUsedPercentage(bool $format = true, string $partition = '/')
    {
        $total = $this->diskTotal(false, $partition);
        $free  = $this->diskFree(false, $partition);

        $percentage = ($total - $free) / $total * 100;

        return $format ? number_format($percentage, 0, '.', '') . '%' : $percentage;
    }

    /**
     * Finds out max PHP execution time limit from php.ini
     *
     * @return int in seconds. If set to zero, no time limit is imposed.
     */
    public function executionTimeLimit(): int
    {
        return (int) ini_get('max_execution_time');
    }

    /**
     * Get the OS hostname
     */
    public function hostname(): string
    {
        return php_uname('n');
    }

    /**
     * Get the OS kernel
     */
    public function kernel(): string
    {
        return php_uname('r');
    }

   /**
    * Finds out PHP memory limit from php.ini
    *
    * @return int|string if $format set to true, return a string like `'2MB'` otherwise, return a raw value in bytes like `2000`
    */
   public function memoryLimit(bool $format = true)
   {
       $usage = $this->getBytesFromPhpIniValue(ini_get('memory_limit'));

       return $format ? $this->byte2size($usage) : $usage;
   }

    /**
     * Returns the amount of memory allocated to PHP
     *
     * @return int|string if $format set to true, return a string like `'2MB'` otherwise, return a raw value in bytes like `2000`
     */
    public function memoryUsage(bool $format = true)
    {
        if ($this->requiredFunction('memory_get_usage')) {
            $usage = memory_get_usage();

            return $format ? $this->byte2size($usage) : $usage;
        }

        return 0;
    }

    /**
     * Get the Os
     */
    public function os(): string
    {
        $parts = explode('\\', static::class);

        return array_pop($parts);
    }

    /**
     * Count the number of memory bars available
     */
    public function ramCount(): int
    {
        return count($this->ramList());
    }

    /**
     * Retrieve the total ram resources.
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    public function ramTotal(bool $format = true)
    {
        $ram = array_sum($this->ramList(false));

        return $format ? $this->byte2size($ram) : $ram;
    }

    /**
     * Get the consumption rate (as a percentage) of the RAM
     *
     * @return int|string if $format set to true, return a string like `'2GB'` otherwise, return a raw value in bytes like `2000000`
     */
    public function ramUsedPercentage(bool $format = true)
    {
        $percentage = $this->ramFree(false) / $this->ramTotal(false) * 100;

        return $format ? number_format($percentage, 0, '.', '') . '%' : $percentage;
    }

    /**
     * Throws an exception if the function does not exist or is disabled
     *
     * @param string $name of the required functions
     */
    protected function requiredFunction(string ...$name)
    {
        $present  = true;
        $disabled = explode(',', ini_get('disable_functions'));

        foreach ($name as $item) {
            if (! function_exists($item)) {
                $present = false;
                // throw new \BadFunctionCallException('Function '.$item.' does not exist.');
                break;
            }
            if (in_array($item, $disabled, true)) {
                $present = false;
                // throw new \BadFunctionCallException('Function '.$name.' is disabled in php.ini.');
                break;
            }
        }

        return $present;
    }

    /**
     * Gives the weight in byte, kb, mb depending on the number of byte passed in the parametre
     */
    protected function byte2size(int $bytes, int $format = 1024, int $precision = 2): string
    {
        return $this->toSize($bytes, ['B', 'KB', 'MB', 'GB', 'TB'], $format, $precision);
    }

    /**
     * Gives the frequency in Hz, MHz, GHz, THz depending on the number of Hz passed in the parametre
     */
    protected function hz2size(int $hz, int $format = 1000, int $precision = 2): string
    {
        return $this->toSize($hz, ['Hz', 'MHz', 'GHz', 'THz'], $format, $precision);
    }

    private function toSize(int $size, array $unit, int $format, int $precision = 2): string
    {
        return @round($size / $format ** ($i = floor(log($size, $format))), $precision) . ' ' . $unit[$i];
    }

    /**
     * Converts shorthand memory notation value to bytes
     *
     * @param string $val Memory size shorthand notation string
     *
     * @return int in bytes
     */
    protected function getBytesFromPhpIniValue($val)
    {
        $val  = trim($val);
        $unit = strtolower($val[strlen($val) - 1]);
        $val  = (int) substr($val, 0, -1);

        switch ($unit) {
            case 'g':
                $val *= 1024;
                // no break;
            case 'm':
                $val *= 1024;
                // no break;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }
}
