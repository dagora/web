<?php

namespace Dagora\WebBundle\Controller;

use Dagora\CoreBundle\Controller\Base\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
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
