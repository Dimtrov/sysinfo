<?php
spl_autoload_register(function($name) {
    $name = str_replace('Dimtrov\\Sysinfo\\', '', $name);
    require_once './src/' . $name . '.php';
});

use Dimtrov\Sysinfo\Sysinfo;

$sysinfo = new Sysinfo();

$info = $sysinfo->all(false, [
    'onlytotalspace' => false,
]);


echo '<pre>'.print_r($info, true).'</pre>';