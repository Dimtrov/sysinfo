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

        $conf = $this->getConfig($conffile);
        if (null === $conf) {
            return $ips;
        }

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

    /**
     * Find mac addresse
     */
    private function findMacAddress(string $conffile, string $delimiter): ?string
    {
        $conf = $this->getConfig($conffile);
        if (null === $conf) {
            return null;
        }

        $pos = strpos($conf, $delimiter);
        if ($pos) {
            // seperate out the mac address
            $str = trim(substr($conf, ($pos + strlen($delimiter))));

            return trim(substr($str, 0, strpos($str, "\n")));
        }

        return null;
    }


    private function getConfig(string $file): ?string
    {        
        $fp = @popen($file, "rb");
        if (!$fp) {
            return null;
        }

        $conf = @fread($fp, 4096);
        if (!$conf) {
            return null;
        }
        @pclose($fp);

        return $conf;
    }
}
