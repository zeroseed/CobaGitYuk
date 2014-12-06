<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LatihanController extends Controller
{

    public function latihanroutingAction()
    {
        return $this->render('AcmeDemoBundle:Latihan:index.html.twig',
        					 array('penulis'=>'Ansera')
        					);
    }


}
