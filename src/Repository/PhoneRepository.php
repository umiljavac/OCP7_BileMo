<?php

namespace App\Repository;

use App\Entity\Phone;

/**
 * @method Phone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phone[]    findAll()
 * @method Phone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneRepository extends AbstractRepository
{
    public function search($term, $order, $limit, $offset, $page)
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.price', $order);

        if ($term) {
            $qb
                ->where('p.mark LIKE ?1')
                ->setParameter(1, '%'.$term.'%');
        }

        return $this->paginate($qb, $limit, $offset, $page);
    }

    public function listPhonesByMark($mark)
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.price')
            ->where('p.mark LIKE ?1')
            ->setParameter(1, '%'.$mark.'%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Phone[] Returns an array of Phone objects
    //     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Phone
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
