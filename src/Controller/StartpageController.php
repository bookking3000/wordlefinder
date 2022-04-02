<?php

namespace App\Controller;

use App\AppName;
use App\Form\WordFinderFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StartpageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @Route("/solver/", name="solver")
     */
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(WordFinderFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $mustContainChars = strtolower(trim($data['charsWhichAreIn']));
            $mustNotContainChars = strtolower(trim($data['notAllowedChars']));

            $charArray = $this->getCharArray($data);
            $chars = implode('', $charArray);
            $length = strlen($chars);

            $wordList = $managerRegistry->getRepository('App:Word')->findLikeWord($chars, $mustContainChars, $mustNotContainChars, $length);
        }

        return $this->render('startpage/index.html.twig', [
            'controller_name' => AppName::FULL,
            'form' => $form->createView(),
            'words' => $wordList ?? null,
        ]);
    }

    /**
     * @param $data
     * @return array
     */
    protected function getCharArray($data): array
    {
        $charArray = [];

        for ($i = 1; $i <= 6; $i++) {
            $char = $data["position_$i"];
            if ($char == null){
                $char = '_';
            }
            if ($char == "-") {
                return $charArray;
            }
            $charArray[] = $char;
        }
        return $charArray;
    }
}
