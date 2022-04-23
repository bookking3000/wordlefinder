<?php

namespace App\Controller;

use App\AppName;
use App\Form\WordFinderFormType;
use DateTime;
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
    protected ManagerRegistry $managerRegistry;

    /**
     * @Route("/", name="home")
     * @Route("/solver/", name="solver")
     */
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $this->request = $request;
        $this->managerRegistry = $managerRegistry;

        $form = $this->createForm(WordFinderFormType::class);
        $form->handleRequest($request);

        if (!$request->isXmlHttpRequest() && $form->isSubmitted() && $form->isValid()) {
            $wordList = $this->getWordList($form);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->handleXmlHttpRequest($form);
        }

        return $this->render('startpage/index.html.twig', [
            'controller_name' => AppName::FULL,
            'form' => $form->createView(),
            'words' => $wordList ?? null,
        ]);
    }

    protected function handleXmlHttpRequest($form): JsonResponse
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $wordList = $this->getWordList($form);
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

    protected function getWordList(FormInterface $form)
    {
        $data = $form->getData();
        $mustHaveChars = strtolower(trim($data['charsWhichAreIn']));
        $forbiddenChars = strtolower(trim($data['notAllowedChars']));

        $charArray = $this->getCharArray($data);
        $wordExpression = implode('', $charArray);
        $length = mb_strlen($wordExpression);

        $words = $this->managerRegistry->getRepository('App:Word')->findLikeWord($wordExpression, $mustHaveChars, $forbiddenChars, $length);

        $forbiddenCharsAtSpecifiedPositions = $this->request->get('forbiddenChars');
        $words = $this->removeWordsWithCharsExcludedAtIndex($words, $forbiddenCharsAtSpecifiedPositions);

        $this->saveRequestEntity($forbiddenChars, $mustHaveChars, $wordExpression, $forbiddenCharsAtSpecifiedPositions);

        return $words;
    }

    protected function removeWordsWithCharsExcludedAtIndex($words, $forbiddenCharsAtSpecifiedPositions)
    {
        if ($forbiddenCharsAtSpecifiedPositions != null) {

            $forbiddenCharsAtSpecifiedPositions = explode(',', $forbiddenCharsAtSpecifiedPositions);

            foreach ($forbiddenCharsAtSpecifiedPositions as $charIndex => $forbiddenCharExpression) {
                if (empty($forbiddenCharExpression))
                    continue;

                foreach ($words as $key => $word) {
                    $observedChar = mb_substr($word, $charIndex - 1, 1);
                    $forbiddenChars = mb_str_split($forbiddenCharExpression);
                    foreach ($forbiddenChars as $forbiddenChar) {
                        if ($forbiddenChar == $observedChar) {
                            unset($words[$key]);
                        }
                    }

                }
            }
        }
        return $words;
    }

    protected function saveRequestEntity(string $forbiddenChars, string $mustHaveChars, string $wordExpression, $forbiddenCharsAtSpecifiedPositions): void
    {
        $request = new \App\Entity\Request();
        $request->setForbiddenChars($forbiddenChars);
        $request->setMustHaveChars($mustHaveChars);
        $request->setWordExpression($wordExpression);
        $request->setIndexedForbiddenQueryExpression(explode(',', $forbiddenCharsAtSpecifiedPositions));
        $request->setTimestamp(new DateTime());
        $this->managerRegistry->getRepository('App:Request')->add($request);
    }
}
