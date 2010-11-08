<?php

namespace Application\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    public function indexAction()
    {
        return $this->render('SiteBundle:Site:index');
    }

    public function contactAction()
    {
        return $this->render('SiteBundle:Site:contact');
    }
}
