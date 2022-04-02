<?php

namespace App\Controller;

use App\AppName;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StartpageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('startpage/index.html.twig', [
            'controller_name' => AppName::FULL,
        ]);
    }
}
