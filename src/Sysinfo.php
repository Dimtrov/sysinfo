<?php

/**
 * This file is part of dimtrov/sysinfo (PHP System Informations).
 *
 * (c) 2022 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrov\Sysinfo;

use BadMethodCallException;
use Dimtrov\Sysinfo\Adapters\BaseAdapter;
use Dimtrov\Sysinfo\Adapters\Linux;
use Dimtrov\Sysinfo\Adapters\Mac;
use Dimtrov\Sysinfo\Adapters\Windows;

/**
 * @method string            cpuArchitecture()
 * @method int               cpuCores()
 * @method int               cpuFree()
 * @method int|string        cpuFrequency(bool $format = true)
 * @method string            cpuName()
 * @method int               cpuProcessors()
 * @method int|string        cpuSpeed(bool $format = true)
 * @method string            cpuVendor()
 * @method int|string        diskCapacity(bool $format = true)
 * @method int               diskCountPartitions()
 * @method int|string        diskFree(bool $format = true, string $partition = '/')
 * @method array             diskPartitions()
 * @method array<string,     int|string> diskPartitionsSpaces(bool $format = true)
 * @method int|string        diskTotal(bool $format = true, string $partition = '/')
 * @method int|string        diskUsed(bool $format = true, string $partition = '/')
 * @method int|string        diskUsedPercentage(bool $format = true, string $partition = '/')
 * @method int|string        diskUsedPercentage(bool $format = true, string $partition = '/')
 * @method int               executionTimeLimit()
 * @method string            hostname()
 * @method string            ipAddress()
 * @method string[]          ipsAddress()
 * @method string            kernel()
 * @method ?string           macAddress()
 * @method int|string        memoryLimit(bool $format = true)
 * @method int|string        memoryUsage(bool $format = true)
 * @method string            os()
 * @method string            osRelease()
 * @method int               ramCount()
 * @method int|string        ramFree(bool $format = true)
 * @method int|string        ramFreePercentage(bool $format = true)
 * @method array<int|string> ramList(bool $format = true)
 * @method int|string        ramTotal(bool $format = true)
 * @method int|string        ramUsed(bool $format = true)
 * @method int|string        ramUsedPercentage(bool $format = true)
 * 
 * @method static string            cpuArchitecture()
 * @method static int               cpuCores()
 * @method static int               cpuFree()
 * @method static int|string        cpuFrequency(bool $format = true)
 * @method static string            cpuName()
 * @method static int               cpuProcessors()
 * @method static int|string        cpuSpeed(bool $format = true)
 * @method static string            cpuVendor()
 * @method static int|string        diskCapacity(bool $format = true)
 * @method static int               diskCountPartitions()
 * @method static int|string        diskFree(bool $format = true, string $partition = '/')
 * @method static int|string        diskFreePercentage(bool $format = true, string $partition = '/')
 * @method static array             diskPartitions()
 * @method static array<string,     int|string> diskPartitionsSpaces(bool $format = true)
 * @method static int|string        diskTotal(bool $format = true, string $partition = '/')
 * @method static int|string        diskUsed(bool $format = true, string $partition = '/')
 * @method static int|string        diskUsedPercentage(bool $format = true, string $partition = '/')
 * @method static int               executionTimeLimit()
 * @method static string            hostname()
 * @method static string            ipAddress()
 * @method static string[]          ipsAddress()
 * @method static string            kernel()
 * @method static ?string           macAddress()
 * @method static int|string        memoryLimit(bool $format = true)
 * @method static int|string        memoryUsage(bool $format = true)
 * @method static string            os()
 * @method static string            osRelease()
 * @method static int               ramCount()
 * @method static int|string        ramFree(bool $format = true)
 * @method static int|string        ramFreePercentage(bool $format = true)
 * @method static array<int|string> ramList(bool $format = true)
 * @method static int|string        ramTotal(bool $format = true)
 * @method static int|string        ramUsed(bool $format = true)
 * @method static int|string        ramUsedPercentage(bool $format = true)
 */
class Sysinfo
{
    /**
     * Available os
     *
     * @var array<string, string>
     */
    private $drivers = [
        'linux'   => Linux::class,
        'mac'     => Mac::class,
        'windows' => Windows::class,
    ];

    /**
     * Active adapter based on server os
     *
     * @var BaseAdapter
     */
    private $adapter;

    /**
     * Self instance for singletton
     * 
     * @var self
     */
    private static $_instance = null;

    public function __construct()
    {
        $agent  = PHP_OS;
        $driver = 'linux';

        if (strpos(strtolower($agent), 'win') !== false) {
            $driver = 'windows';
        }

        if ($agent === 'Darwin') {
            $driver = 'mac';
        }

        $this->adapter = new $this->drivers[$driver]();
    }

    /**
     * Singleton method
     */
    public static function instance(): self
    {
        if (! (self::$_instance instanceof self)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Get informations grouped by category
     */
    public static function all(bool $merge = false, array $options = []): array
    {
        $options = array_merge([
            'format'    => true,
            'partition' => '/'
        ], $options);

        /**
         * @var array<string, array<string, string|int|null|string[]>>
         */
        $informations = [
            'computer' => self::computer(),
            'cpu'      => self::cpu($options['format']),
            'disk'     => self::disk($options['format'], $options['partition']),
            'php'      => self::php($options['format']),
            'ram'      => self::ram($options['format']),
        ];

        if (! $merge) {
            return $informations;
        }

        $items = [];

        foreach ($informations as $key => $values) {
            foreach ($values as $k => $v) {
                $items[$key . ucfirst($k)] = $v;
            }
        }

        return $items;
    }

    /**
     * Get informations about the computer
     */
    public static function computer(): array
    {
        $adapter = self::instance()->adapter;

        return [
            'hostname'   => $adapter->hostname(),
            'ipAddress'  => $adapter->ipAddress(),
            'ipsAddress' => $adapter->ipsAddress(),
            'kernel'     => $adapter->kernel(),
            'macAddress' => $adapter->macAddress(),
            'os'         => $adapter->os(),
            'osRelease'  => $adapter->osRelease(),
        ];
    }

    /**
     * Get informations about the CPU
     */
    public static function cpu(bool $format = true): array
    {
        $adapter = self::instance()->adapter;

        return [
            'architecture' => $adapter->cpuArchitecture(),
            'cores'        => $adapter->cpuCores(),
            'free'         => $adapter->cpuFree(),
            'frequency'    => $adapter->cpuFrequency($format),
            'name'         => $adapter->cpuName(),
            'name'         => $adapter->cpuName(),
            'processors'   => $adapter->cpuProcessors(),
            'speed'        => $adapter->cpuSpeed($format),
            'vendor'       => $adapter->cpuVendor(),
        ];
    }

    /**
     * Get informations about the HARD DISK
     */
    public static function disk(bool $format = true, string $partition = '/'): array
    {
        $adapter = self::instance()->adapter;

        return [
            'capacity'         => $adapter->diskCapacity($format),
            'countPartitions'  => $adapter->diskCountPartitions(),
            'free'             => $adapter->diskFree($format, $partition),
            'freePercentage'   => $adapter->diskFreePercentage($format, $partition),
            'partitions'       => $adapter->diskPartitions(),
            'partitionsSpaces' => $adapter->diskPartitionsSpaces($format),
            'total'            => $adapter->diskTotal($format, $partition),
            'used'             => $adapter->diskUsed($format, $partition),
            'usedPercentage'   => $adapter->diskUsedPercentage($format, $partition),
        ];
    }

    /**
     * Get informations about the PHP
     */
    public static function php(bool $format = true): array
    {
        $adapter = self::instance()->adapter;

        return [
            'memoryLimit'        => $adapter->memoryLimit($format),
            'memoryUsage'        => $adapter->memoryUsage($format),
            'executionTimeLimit' => $adapter->executionTimeLimit(),
        ];
    }

    /**
     * Get informations about the RAM
     */
    public static function ram(bool $format = true): array
    {
        $adapter = self::instance()->adapter;

        return [
            'count'          => $adapter->ramCount(),
            'free'           => $adapter->ramFree($format),
            'freePercentage' => $adapter->ramFreePercentage($format),
            'list'           => $adapter->ramList($format),
            'total'          => $adapter->ramTotal($format),
            'used'           => $adapter->ramUsed($format),
            'usedPercentage' => $adapter->ramUsedPercentage($format),
        ];
    }

    /**
     * Magic call to adapter methods
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments = [])
    {
        if (method_exists($this->adapter, $method)) {
            return call_user_func_array([$this->adapter, $method], $arguments);
        }

        throw new BadMethodCallException("Method `{$method}` not exist");
    }

    /**
     * Magic statical call to adapter methods
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments = [])
    {
        return self::instance()->__call($method, $arguments);
    }
}
