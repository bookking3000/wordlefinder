<?php

namespace App\Controller;

use App\AppName;
use App\Form\WordFinderFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    /**
     * @Route("/about", name="about")
     */
    public function index(Request $request): Response
    {
        return $this->render('about/index.html.twig', [
            'controller_name' => AppName::FULL,
        ]);
    }

}
