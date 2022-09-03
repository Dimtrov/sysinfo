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

class Linux extends BaseAdapter
{
    /**
     * Contains all the memory stats from 'free'.
     *
     * @var array
     */
    private $ram_resources;

    /**
     * Retrieves all memory statistics from 'free' that we need.
     *
     * @return void
     */
    public function __construct()
    {
        $free = shell_exec('free');
        $free = (string) trim($free);

        $free_arr = explode("\n", $free);

        $mem = explode(' ', $free_arr[1]);
        $mem = array_filter($mem);

        $this->ram_resources = array_merge($mem);
    }

    /**
     * {@inheritDoc}
     */
    public function cpuCores(): int
    {
        if (! is_file('/proc/cpuinfo')) {
            return 1;
        }

        $cpu = file_get_contents('/proc/cpuinfo');

        preg_match_all('/^processor/m', $cpu, $matches);

        return count($matches[0]);
    }

    /**
     * {@inheritDoc}
     */
    public function cpuFree()
    {
        // @todo implement

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function cpuFrequency(bool $format = true)
    {
        // @todo implement

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function cpuName(): string
    {
        $row   = shell_exec("cat /proc/cpuinfo | grep 'model name' | uniq");
        $array = explode(':', $row);

        return trim($array[1]);
    }

    /**
     * {@inheritDoc}
     */
    public function cpuProcessors(): int
    {
        // @todo implement

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function cpuSpeed(bool $format = true)
    {
        // @todo implement

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function cpuVendor(): string
    {
        // @todo implement

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function diskPartitions(): array
    {
        // @todo implement

        return [0];
    }

    /**
     * {@inheritDoc}
     */
    public function ramFree(bool $format = true)
    {
        $ram = 1024 * (int) $this->ram_resources[2];

        return $format ? $this->byte2size($ram) : $ram;
    }

    /**
     * {@inheritDoc}
     */
    public function ramList(bool $format = true): array
    {
        // @todo implement

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function ramTotal(bool $format = true)
    {
        $ram = 1024 * (int) $this->ram_resources[1];

        return $format ? $this->byte2size($ram) : $ram;
    }
}
