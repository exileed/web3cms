<?php

namespace Application\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;
use Application\SiteBundle\Model\Contact;

class SiteController extends Controller
{
    public $adminEmailAddress = 'Web3CMS Staff <phpdevmd@web3cms.com>';

    /**
     * Homepage
     */
    public function indexAction()
    {
        return $this->render('SiteBundle:Site:index');

        // render a Twig template instead
        // return $this->render('SiteBundle:Site:index:twig');
    }

    /**
     * Contact us page
     * Display one simple form to contact site owner, and process it.
     */
    public function contactAction()
    {
        $contact = new Contact();

        $form = new Form('contact', $contact, $this->container->getValidatorService());
        $form->add(new TextField('name'));
        $form->add(new TextField('email'));
        $form->add(new TextField('subject'));
        $form->add(new TextareaField('content'));

        if ($this['request']->getMethod() == 'POST') {
            $form->bind($this['request']->get('contact'));

            if ($form->isValid()) {
                $headers = "From: {$form['email']->getData()}\r\nReply-To: {$form['email']->getData()}";
                @mail($this->adminEmailAddress, $form['subject']->getData(), $form['content']->getData(), $headers);

                $this['request']->getSession()->setFlash('topSummary', '<strong>Thank you</strong> for contacting us. We will respond to you as soon as possible.');

                // we need redirect for 2 reasons: reset form and avoid displaying flash message twice
                return $this->redirect($this->generateUrl('contact'));
            } else {
                //die($this->container->getValidatorService()->validate($form));
                $this['request']->getSession()->setFlash('topError', 'An error occured while validating this form.'); // При проверке данной формы произошла ошибка.
                $response = $this->render('SiteBundle:Site:contact', array('form' => $form));
                // unset flash message so it does not show up on the next page load
                $this['request']->getSession()->setFlash('topError', null);

                return $response;
            }
        }

        return $this->render('SiteBundle:Site:contact', array('form' => $form));
    }
}
