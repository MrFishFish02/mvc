<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class index extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {

        $ttt = "11";
        return $this->render('index.html.twig', [
            'ttt' => $ttt,
        ]);
    }
}
