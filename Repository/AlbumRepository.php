<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use MelisPlatformFrameworkSymfonyDemoToolLogic\Entity\Album;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Album::class);
    }

    /**
     * @param string $search
     * @param array $searchableColumns
     * @param null $orderBy
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getAlbum($search = '', $searchableColumns = [], $orderBy = null, $order = null, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($searchableColumns) && !empty($search)) {
            foreach ($searchableColumns as $column) {
                $qb->orWhere("a.$column LIKE :search");
            }
            $qb->setParameter('search', '%'.$search.'%');
        }
        if(!empty($orderBy))
            $qb->orderBy("a.$orderBy", $order);

        if(!empty($offset))
            $qb->setFirstResult($offset);

        if(!empty($limit))
            $qb->setMaxResults($limit);

        $qb->groupBy('a.alb_id');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalRecords()
    {
        $qb = $this->createQueryBuilder('a')->select("count(a.alb_id)");
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }
}
