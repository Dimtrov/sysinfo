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

        return trim(str_ireplace('Name ', '', $cpu));
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

        return trim(str_ireplace('Manufacturer ', '', $cpu));
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

    public function macAddress(): ?string
    {
        // this windows version works on xp running apache
        // based server. it has not been tested with anything
        // else, however it should work with NT, and 2000 also
        // execute the ipconfig
        @exec('ipconfig/all', $lines);
        // count number of lines, if none returned return MAC_404
        // thanks go to Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>
        if (count($lines) === 0) {
                return null;
        }
        // $path the lines together
        $conf = implode(PHP_EOL, $lines);
            
        $lines = explode(PHP_EOL, $conf);
        foreach ($lines as $key => $line) {
            // check for the mac signature in the line
            // originally the check was checking for the existence of string 'physical address'
            // however Gert-Rainer Bitterlich pointed out this was for english language
            // based servers only. preg_match updated by Gert-Rainer Bitterlich. Thanks
            if (preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $line)) {
                $trimmedLine = trim($line);
                // take of the mac addres and return
                return trim(substr($trimmedLine, strrpos($trimmedLine, " ")));
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function osRelease(): string
    {
        $wmic = explode(PHP_EOL, shell_exec('wmic OS get Caption'));

        return trim($wmic[1] ?? '');
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
