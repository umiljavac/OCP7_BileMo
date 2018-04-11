<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository
{

    public function search($client, $term, $order = 'asc', $limit, $offset, $page)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->andWhere('u.client = :val')
            ->setParameter('val', $client)
            ->select('u')
            ->orderBy('u.id', $order)
        ;

        if ($term) {
            $qb
                ->where('u.roles LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }

        return $this->paginate($qb, $limit, $offset, $page);
    }

    /**
     * @param $client
     * @return mixed
     */
    public function findByClient($client)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.client = :val')
            ->setParameter('val', $client)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $client
     * @param $id
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByClient($client, $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.client = :val')
            ->setParameter('val', $client)
            ->andWhere('u.id = :val2')
            ->setParameter('val2', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
