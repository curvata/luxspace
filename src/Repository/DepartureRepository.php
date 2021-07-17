<?php

namespace App\Repository;

use App\Class\SearchFlight;
use App\Entity\Departure;
use App\Entity\Location;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DepartureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Departure::class);
    }
    
    /**
     * Retourne les vols allers disponibles pour la date recherchée
     */
    public function findFlights(SearchFlight $search): ?array
    {
        return $this->createQueryBuilder('d')
            ->where('d.destination = :destination_id')
            ->andWhere('d.seat >= :seat')
            ->andWhere('d.date BETWEEN :date1 AND :date2')
            ->orderBy('d.price', 'ASC')
            ->setParameters(
                [
                'destination_id' => $search->getDestination()->getId(),
                'seat' => $search->getPassagers(),
                'date1' => $search->getDeparture()->format('Y-m-d'),
                'date2' => $search->getDeparture()->modify('+1 day')->format('Y-m-d'),
                ]
            )
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Retourne la liste des dates avec des vols allers disponibles pour la destination
     */
    public function findValidDateFlight(Location $location): ?array
    {
        return $this->createQueryBuilder('d')
            ->select('d.date')
            ->where('d.destination = :destination_id')
            ->andWhere('d.date BETWEEN :date1 AND :date2')
            ->setParameters(
                [
                'destination_id' => $location->getId(),
                'date1' => (new DateTime()),
                'date2' => (new DateTime())->modify('+1 years')->format('Y-m-d'),
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
