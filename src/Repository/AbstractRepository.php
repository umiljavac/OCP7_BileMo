<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 25/03/2018
 * Time: 16:43
 */

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends EntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit, $offset, $page = null)
    {
        if (0 == $limit) {
            throw new \LogicException('$limit & $offset must be greater than 0.');
        }
        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));

        $page !== null ? $currentPage = $page : $currentPage = ceil(($offset + 1) / $limit);

        $pager->setMaxPerPage((int) $limit);

        $pager->setCurrentPage((int) $currentPage);

        return $pager;
    }
}
