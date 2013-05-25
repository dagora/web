<?php

namespace Dagora\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Homepage
     *
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render('DagoraWebBundle:Home:index.html.twig');
    }
}
