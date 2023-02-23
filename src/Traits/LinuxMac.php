<?php

namespace Dimtrov\Sysinfo\Traits;

/**
 * Common trait for Linux and Mac platforms
 */
trait LinuxMac
{
    /**
     * Find all the ips addresses
     */
    private function findIps(string $conffile, string $ipdelim): array
    {
        $ips = [];

        $fp = @popen($conffile, 'rb');
        if (!$fp) {
            return $ips;
        }
        
        $conf = @fread($fp, 4096);
        @pclose($fp);

        $lines = explode(PHP_EOL, $conf);
        
        $num = "(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
        
        foreach ($lines as $key => $line) {
            // check for the ip signature in the line
            if (!preg_match("/^$num\\.$num\\.$num\\.$num$/", $line) && strpos($line, $ipdelim)) {
                // seperate out the ip
                $ip = substr($line, strpos($line, $ipdelim) + strlen($ipdelim));
                $ips[] = trim(substr($ip, 0, strpos($ip, " ")));
            }
        }
        
        return array_filter($ips, function ($ip) { return !empty($ip); });
    }
}
