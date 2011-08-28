<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

use Symfony\Component\DependencyInjection\Definition;

$this->import($_SERVER['SYMFONY__KERNEL__ROOT_DIR'].'/config/config_test.yml');

$container->loadFromExtension('framework', array(
    'router' => array('resource' => __DIR__.'/routing.php'),
    'secret' => 'secret',
));

$definition = new Definition('Symfony\Component\Security\Core\User\InMemoryUserProvider', array(array(
    'example' => array(
        'password' => '72bca7bae83bb144e12cafeed25a7077fed3b3c1',  // salted sha1('1'.'secret') and stretched
    ),
    'admin' => array(
        'password' => 'a72b7cac5091690f398e1c8ef700cfd2333c687d',  // salted sha1('2'.'secret') and stretched
    ),
)));
$definition->addTag('societo.auth.user.provider');
$container->setDefinition('societo.page.test.user_provider', $definition);
