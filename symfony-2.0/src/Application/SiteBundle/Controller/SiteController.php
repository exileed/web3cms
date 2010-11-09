<?php

namespace Application\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;
use Application\SiteBundle\Model\Contact;

class SiteController extends Controller
{
    /**
     * Homepage
     */
    public function indexAction()
    {
        return $this->render('SiteBundle:Site:index');
    }

    /**
     * Contact us page - has one simple form to contact site owner.
     */
    public function contactAction()
    {
        $contact = new Contact();

        $form = new Form('contact', $contact, $this->container->getValidatorService());
        $form->add(new TextField('name'));
        $form->add(new TextField('email'));
        $form->add(new TextField('subject'));
        $form->add(new TextareaField('content'));

        return $this->render('SiteBundle:Site:contact', array('form' => $form));
    }
}
