<?php

if (!isset($_SERVER['KERNEL_DIR'])) {
    $_SERVER['KERNEL_DIR'] = __DIR__.'/../../../../../app';
}

$_SERVER['SYMFONY__KERNEL__ROOT_DIR'] = $_SERVER['KERNEL_DIR'];

require_once $_SERVER['KERNEL_DIR'].'/bootstrap.php.cache';
