<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class about extends AbstractController
{
    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        $ttt = "11";
        return $this->render('about.html.twig', [
            'ttt' => $ttt,
        ]);
    }
}
// $this->get('request')->getSchemeAndHttpHost();