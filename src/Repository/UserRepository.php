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
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
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
