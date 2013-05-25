<?php

namespace Dagora\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse;

class SourceController extends Controller
{
    /**
     * View a source with all the data
     *
     * @Route("/sources/{source_id}", requirements={"source_id" = "\d+"}, name="source_show")
     * @Method({"GET"})
     * @Template()
     *
     * @param  int $source_id
     */
    public function showAction($source_id)
    {
        return $this->render('DagoraWebBundle:Source:show.html.twig',
            array(
                'source' => $source_id
            ));
    }
}
