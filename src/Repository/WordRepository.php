<?php

namespace App\Repository;

use App\Entity\Word;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Word|null find($id, $lockMode = null, $lockVersion = null)
 * @method Word|null findOneBy(array $criteria, array $orderBy = null)
 * @method Word[]    findAll()
 * @method Word[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
    }

    public function add(Word $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Word $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByLength($length): Query
    {
        $query = $this->createQueryBuilder('w')
            ->andWhere('w.length = :length');

        $query->setParameter('length', $length);

        return $query->getQuery();
    }

    public function findByStart($wordStartExpression)
    {
        $query = $this->createQueryBuilder('w')
            ->andWhere('w.word LIKE :val');

        $query->setParameter('val', $wordStartExpression);
        return $query->getQuery()->getResult();
    }

    public function findLikeWord($wordExpression, $mustHaveChars, $forbiddenChars, $length)
    {
        $mustHaveCharsAry = str_split($mustHaveChars);
        $forbiddenCharsAry = str_split($forbiddenChars);

        $query = $this->createQueryBuilder('w')
            ->andWhere('w.word LIKE :val')
            ->andWhere('w.length = :length');

        $query->setParameter('val', $wordExpression);
        $query->setParameter('length', $length);

        $i = 0;
        foreach ($forbiddenCharsAry as $forbiddenChar) {
            $forbiddenCharsQuery = '%' . $forbiddenChar . '%';
            $query->andWhere("w.word NOT LIKE :fcq$i");
            $query->setParameter("fcq$i", $forbiddenCharsQuery);
            $i++;
        }

        $j = 0;
        foreach ($mustHaveCharsAry as $mustHaveChar) {
            $mustHaveCharsQuery = '%' . $mustHaveChar . '%';
            $query->andWhere("w.word LIKE :mhcq$j");
            $query->setParameter("mhcq$j", $mustHaveCharsQuery);
            $j++;
        }

        return $query->getQuery()->getResult();
    }

}
