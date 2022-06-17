<?php

namespace App\Controller\Words;

use App\AppName;
use App\Entity\Word;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SluggedWordListController extends AbstractController
{
    /**
     * @Route("/words/slugged/word/list", name="app_words_slugged_word_list")
     * @Route("/words/{startingchars}/", name="startingchars_slugged" )
     */
    public function startingWithChars(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $startChars = $request->get('startingchars');
        $words = $managerRegistry->getRepository('App:Word')->findByStart(
            $startChars .'%'
        );

        return $this->render('words/slugged_word_list/index.html.twig', [
            'controller_name' => AppName::FULL,
            'words' => $words,
            'startChars' => $startChars,
        ]);
    }

}
