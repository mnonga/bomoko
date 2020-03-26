<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response; //added

class DefaultController extends Controller
{
    public function indexAction()
    {
        //return new Response("Hello World !");
        //return $this->render('UserBundle:Default:index.html.twig');
        return $this->render('@User/Default/index.html.twig');
    }
}
