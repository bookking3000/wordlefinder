<?php

namespace App\Controller;

use App\AppName;
use App\Form\WordFinderFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StartpageController extends AbstractController
{
    protected Request $request;


    /**
     * @Route("/", name="home")
     * @Route("/solver/", name="solver")
     */
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $this->request = $request;
        $form = $this->createForm(WordFinderFormType::class);
        $form->handleRequest($request);

        if (!$request->isXmlHttpRequest() && $form->isSubmitted() && $form->isValid()) {
            $wordList = $this->getWordList($form, $managerRegistry);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->handleXmlHttpRequest($form, $managerRegistry);
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
            if ($char == null) {
                $char = '_';
            }
            if ($char == "-") {
                return $charArray;
            }
            $charArray[] = $char;
        }
        return $charArray;
    }

    /**
     * @param FormInterface $form
     * @param ManagerRegistry $managerRegistry
     * @return float|int|mixed|string
     */
    protected function getWordList(FormInterface $form, ManagerRegistry $managerRegistry)
    {
        $data = $form->getData();
        $mustContainChars = strtolower(trim($data['charsWhichAreIn']));
        $mustNotContainChars = strtolower(trim($data['notAllowedChars']));

        $charArray = $this->getCharArray($data);
        $chars = implode('', $charArray);
        $length = strlen($chars);

        $words = $managerRegistry->getRepository('App:Word')->findLikeWord($chars, $mustContainChars, $mustNotContainChars, $length);
        $words = $this->removeWordsWithCharsExcludedAtIndex($words);

        return $words;
    }

    protected function handleXmlHttpRequest($form, $managerRegistry): JsonResponse
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $wordList = $this->getWordList($form, $managerRegistry);
            return $this->json([
                'status' => 'success',
                'form' => $this->render('startpage/index.html.twig', [
                    'controller_name' => AppName::FULL,
                    'form' => $form->createView(),
                    'words' => $wordList ?? null,
                ]),
            ]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            return $this->json([
                'status' => 'success',
                'form' => $this->render('startpage/index.html.twig', [
                    'controller_name' => AppName::FULL,
                    'form' => $form->createView(),
                    'words' => null,
                ]),
            ]);
        }
        return $this->json([
            'status' => 'error',
        ]);
    }

    /**
     * @param $words
     * @return mixed
     */
    protected function removeWordsWithCharsExcludedAtIndex($words)
    {
        $forbiddenCharsAtSpecifiedPositions = $this->request->get('forbiddenChars');
        if ($forbiddenCharsAtSpecifiedPositions != null) {
            $forbiddenCharsAtSpecifiedPositions = explode(',', $forbiddenCharsAtSpecifiedPositions);
            foreach ($forbiddenCharsAtSpecifiedPositions as $charIndex => $forbiddenChar) {
                if (empty($forbiddenChar))
                    continue;

                foreach ($words as $key => $word) {
                    $char = (substr($word, $charIndex - 1, 1));
                    if ($char == $forbiddenChar)
                        unset($words[$key]);
                }
            }
        }
        return $words;
    }
}
