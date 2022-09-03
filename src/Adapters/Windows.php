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

class Windows extends BaseAdapter
{
    /**
     * {@inheritDoc}
     */
    public function cpuCores(): int
    {
        $cpu = shell_exec('wmic cpu get NumberOfCores');

        return (int) str_replace('NumberOfCores', '', $cpu);
    }

    /**
     * {@inheritDoc}
     */
    public function cpuFree()
    {
        $cpu = shell_exec('wmic cpu get LoadPercentage');

        return (int) str_replace('LoadPercentage ', '', $cpu);
    }

    /**
     * {@inheritDoc}
     */
    public function cpuFrequency(bool $format = true)
    {
        $cpu = $this->cpuName();

        $parts = explode('@', $cpu);

        return $parts[1] ?? 0;
    }

    /**
     * {@inheritDoc}
     */
    public function cpuName(): string
    {
        $cpu = shell_exec('wmic cpu get Name');

        return str_ireplace('Name ', '', $cpu);
    }

    /**
     * {@inheritDoc}
     */
    public function cpuProcessors(): int
    {
        $cpu = shell_exec('wmic cpu get NumberOfLogicalProcessors');

        return (int) str_ireplace('NumberOfLogicalProcessors ', '', $cpu);
    }

    /**
     * {@inheritDoc}
     */
    public function cpuSpeed(bool $format = true)
    {
        $cpu = shell_exec('wmic cpu get CurrentClockSpeed');

        $cpu = 1000 * (int) trim(str_ireplace('CurrentClockSpeed ', '', $cpu));

        return $format ? $this->hz2size($cpu) : $cpu;
    }

    /**
     * {@inheritDoc}
     */
    public function cpuVendor(): string
    {
        $cpu = shell_exec('wmic cpu get Manufacturer');

        return str_ireplace('Manufacturer ', '', $cpu);
    }

    /**
     * {@inheritDoc}
     */
    public function diskPartitions(): array
    {
        $drives = shell_exec('fsutil fsinfo drives');

        if (! is_string($drives)) {
            return [];
        }

        $drives = explode(' ', str_ireplace(['Drives: ', 'Lecteurs'], '', $drives));

        $partitions = array_filter($drives, static fn ($drive) => is_dir($drive));

        return $partitions;
    }

    /**
     * {@inheritDoc}
     */
    public function osRelease(): string
    {
        $wmic = explode(PHP_EOL, shell_exec('wmic OS get Caption'));

        return $wmic[1] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function ramFree(bool $format = true)
    {
        $ram = shell_exec('wmic OS get FreePhysicalMemory /Value');

        $ram = 1024 * (int) str_replace('FreePhysicalMemory=', '', $ram);

        return $format ? $this->byte2size($ram) : $ram;
    }

    /**
     * {@inheritDoc}
     */
    public function ramList(bool $format = true): array
    {
        $ram = shell_exec('wmic memorychip get capacity');

        if (! is_string($ram)) {
            return [];
        }

        $ram = trim(str_ireplace('Capacity ', '', $ram));

        $list = explode(' ', $ram);

        return array_map(fn ($v) => $format ? $this->byte2size((int) $v) : $v, $list);
    }

    /**
     * {@inheritDoc}
     */
    public function ramUsedPercentage(bool $format = true)
    {
        $percentage = 100 - ($this->ramFree(false) / $this->ramTotal(false) * 100);

        return $format ? number_format($percentage, 0, '.', '') . '%' : $percentage;
    }
}
