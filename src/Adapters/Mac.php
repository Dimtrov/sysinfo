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

use Dimtrov\Sysinfo\Traits\LinuxMac;

class Mac extends BaseAdapter
{
    use LinuxMac;

    /**
     * {@inheritDoc}
     */
    public function cpuCores(): int
    {
        return (int) trim(shell_exec('sysctl -n hw.ncpu'));
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
        return shell_exec('sysctl -n machdep.cpu.brand_string');
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
    public function ipsAddress(): array
    {
        $ips = array_merge(
            parent::ipsAddress(),
            $this->findIps('/sbin/ifconfig', 'inet ')
        );
        
        return array_unique(array_filter($ips, function ($ip) { return !empty($ip); }));
    }

    /**
     * {@inheritDoc}
     */
    public function osRelease(): string
    {
        return trim(shell_exec('uname -rs'));
    }

    /**
     * {@inheritDoc}
     */
    public function ramFree(bool $format = true)
    {
        $ram = 1024 * (int) shell_exec("ps -caxm -orss= | awk '{ sum += $1 } END { print sum }'"); // KB

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
        $total_ram = (int) shell_exec('sysctl -n hw.memsize');

        if ($total_ram) {
            return (int) $total_ram;
        }

        $total_ram = shell_exec("hostinfo | grep 'Primary memory available:'");
        $total_ram = (int) str_replace(['Primary memory available: ', ' gigabytes'], '', $total_ram);

        $total_ram *= 1024 * 1024 * 1025;

        return $format ? $this->byte2size($total_ram) : $total_ram;
    }
}
