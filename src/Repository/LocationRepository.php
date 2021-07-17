<?php

namespace App\Repository;

use App\Entity\Location;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }
    
    /**
     * Retourne les autres destinations
     */
    public function findWithNoThisLocation(Location $location): ?array
    {
        return $this->createQueryBuilder('l')
            ->select('min(d.price) minPrice, l')
            ->join('App\Entity\departure', 'd', 'WITH', 'd.destination = l')
            ->where('l.id <> :id')
            ->andWhere('d.date > :date')
            ->groupBy('l')
            ->setParameter('id', $location->getId())
            ->setParameter('date', new DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne jusqu'à 4 destinations populaires
     */
    public function findPopularDestination(): ?array
    {
        $popular = $this->createQueryBuilder('l')
            ->select('l.id, count(r) resa')
            ->join('App\Entity\departure', 'd', 'WITH', 'd.destination = l')
            ->join('App\Entity\reservation', 'r', 'WITH', 'r.departure = d')
            ->where('r.status IN (1, 2)')
            ->groupBy('l')
            ->orderBy('resa', 'DESC')
            ->getQuery()
            ->setMaxResults(4)
            ->getResult();

        if ($popular != null) {
            foreach ($popular as $key => $value) {
                $popular[] = $value['id'];
                unset($popular[$key]);
            }
            return $this->createQueryBuilder('l')
                ->select('l, min(d.price) minPrice')
                ->join('App\Entity\departure', 'd', 'WITH', 'l = d.destination')
                ->where('l.id IN ('.implode(', ', $popular).')')
                ->groupBy('l')
                ->getQuery()
                ->setMaxResults(4)
                ->getResult();
        } else {
            return null;
        }
    }
    
    /**
     * Retourne les destinations avec le prix du départ le moins cher
     */
    public function findAllLocationWithDepartureMinPrice(): ?array
    {
        return $this->createQueryBuilder('l')
            ->select('min(d.price) minPrice, l')
            ->join('App\Entity\departure', 'd', 'WITH', 'd.destination = l')
            ->where('d.date > :date')
            ->groupBy('l')
            ->setParameter('date', new DateTime())
            ->getQuery()
            ->getResult();
    }
}
