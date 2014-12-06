<?php

namespace Lathain\BayuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LathainBayuBundle:Default:index.html.twig', array('name' => $name));
    }
}
