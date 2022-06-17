<?php

namespace App\Controller\API\Word;

use App\Entity\Word;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/words", name="word_api")
 */
class WordApiController extends AbstractController
{

    /**
     * @Route("/by-length", name="api_words_by_length", methods={"GET"})
     */
    public function by_length(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $length = $request->get("length");
        $page = $request->get("page", 0);

        if ($length == null) {
            return $this->json([
                'status' => 'NOK',
                'message' => 'There was an Error getting the length Parameter from your Request.',
            ], 400);
        }

        /** @var Query $wordQuery */
        $wordQuery = $managerRegistry->getRepository('App:Word')->findByLength($length);

        $pageSize = 50;
        $wordQueryPaginator = new Paginator($wordQuery);
        $totalItems = count($wordQueryPaginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $wordQueryPaginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page))
            ->setMaxResults($pageSize);

        $result = [];
        foreach ($wordQueryPaginator as $word)
            $result[] = $word->getWord();

        return $this->json([
            'status' => 'OK',
            'result' => $result,
            'pages' => $pagesCount
        ]);
    }

    /**
     * @Route("/is-known", name="api_word_is_known", methods={"GET"})
     */
    public function is_known(Request $request, EntityManagerInterface $entityManager): Response
    {
        $word = $request->get("word");
        if ($word == null) {
            return $this->json([
                'status' => 'NOK',
                'message' => 'There was an Error getting the word Parameter from your Request.',
            ], 400);
        }

        $wordList = $entityManager->getRepository(Word::class)->findBy([
            'word' => $word,
        ]);

        $boolResult = count($wordList) > 0;
        return $this->json([
            'status' => 'OK',
            'result' => $boolResult,
        ]);
    }


}
