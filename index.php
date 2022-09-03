<?php
spl_autoload_register(function($name) {
    $name = str_replace('Dimtrov\\Sysinfo\\', '', $name);
    require_once './src/' . $name . '.php';
});

use Dimtrov\Sysinfo\Sysinfo;

$sysinfo = new Sysinfo();

$ram = [
    'total' => $sysinfo->cpuFree(),
    'speed' => $sysinfo->cpuSpeed(),
    'basespeed' => $sysinfo->cpuFrequency(),

];


echo '<pre>'.print_r($ram, true).'</pre>';