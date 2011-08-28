<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Resource\FileResource;

$collection = new RouteCollection();
$collection->addCollection($loader->import($_SERVER['SYMFONY__KERNEL__ROOT_DIR'].'/config/routing.yml'));
$collection->addCollection($loader->import(__DIR__.'/routing.yml'));

return $collection;
