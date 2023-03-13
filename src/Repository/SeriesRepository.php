<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Series>
 *
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }


    /**
     * cette methode permet de trouver des series publiques basée sur le nombre de series 
     *
     * @param integer $nbSeries
     * @return array
     */
    public function findPublicSerie(?int $nbSeries): array
    {
        sleep(3);
        $queryBuilder = $this->createQueryBuilder('r')
            ->where('r.isPublic = 1')
            ->orderBy('r.createdAt', 'DESC');


        if ($nbSeries !== 0 || $nbSeries !== null) {
            $queryBuilder->setMaxResults($nbSeries);
        }

        return $queryBuilder->getQuery()
            ->getResult();
    }


}
