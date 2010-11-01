<?php

namespace Application\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UserBundle:User:index', array('name' => $name));

        // render a Twig template instead
        // return $this->render('UserBundle:User:index:twig', array('name' => $name));
    }
}
