<?php

namespace App\Controller\API\Word;

use App\AppName;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController extends AbstractController
{
    /**
     * @Route("/api/documentation/words", name="app_api_words_documentation")
     */
    public function index(): Response
    {
        return $this->render('api/words/documentation/index.html.twig', [
            'controller_name' => AppName::FULL,
        ]);
    }
}
