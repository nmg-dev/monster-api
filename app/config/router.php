<?php

$router = $di->getRouter();
$router->add('/:service/:action', ['controller'=>1]);
$router->handle();
