<?php

namespace App\Repository;

use App\Class\SearchFlight;
use App\Entity\Location;
use App\Entity\Returned;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReturnedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Returned::class);
    }
    
    /**
     * Retourne les vols retours disponibles pour la date recherchée
     */
    public function findFlights(SearchFlight $search): ?array
    {
        return $this->createQueryBuilder('d')
            ->where('d.ffrom = :destination_id')
            ->andWhere('d.seat >= :seat')
            ->andWhere('d.date BETWEEN :date AND :date1')
            ->orderBy('d.price', 'ASC')
            ->setParameters(
                [
                'destination_id' => $search->getDestination()->getId(),
                'seat' => $search->getPassagers(),
                'date' => $search->getReturned()->format('Y-m-d'),
                'date1' => $search->getReturned()->modify('+1 day')->format('Y-m-d'),
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la liste des dates avec des vols retours disponibles pour la location
     */
    public function findValidDateFlight(Location $location): ?array
    {
        return $this->createQueryBuilder('d')
            ->select('d.date')
            ->where('d.ffrom = :destination_id')
            ->andWhere('d.seat > 0')
            ->andWhere('d.date BETWEEN :date AND :date1')
            ->setParameters(
                [
                'destination_id' => $location->getId(),
                'date' => (new DateTime()),
                'date1' => (new DateTime())->modify('+1 years')->format('Y-m-d'),
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
