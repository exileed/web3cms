<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->addRoute('hey', new Route('/hey/:name', array(
    '_controller' => 'SiteBundle:Site:index',
)));

return $collection;
