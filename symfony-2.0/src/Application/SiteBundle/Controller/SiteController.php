<?php

namespace Application\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SiteBundle:Site:index', array('name' => $name));

        // render a Twig template instead
        // return $this->render('SiteBundle:Site:index:twig', array('name' => $name));
    }
}
