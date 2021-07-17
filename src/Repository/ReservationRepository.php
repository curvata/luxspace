<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }
    
    /**
     * Retourne toutes les réservations payées ou remboursées
     */
    public function findAll(): ?array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status IN (1, 2)')
            ->orderBy('r.createAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Retourne les réservations non payées
     */
    public function findNotPaidReservation(User $user) : ?array
    {
        return $this->createQueryBuilder('r')
            ->where('r.client = :user')
            ->andWhere('r.status = 0')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Retourne les réservations payées ou remboursées d'un client
     */
    public function findReservationPaidAndRefund(User $user): ?array
    {
        return $this->createQueryBuilder('r')
            ->where('r.client = :client')
            ->andWhere('r.status IN (1, 2)')
            ->setParameter('client', $user)
            ->orderBy('r.createAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Retourne la réservation avec la date de départ la plus proche
     */
    public function findShortDepartureDate(User $user): ?array
    {
        return $this->createQueryBuilder('r')
            ->select('r, d.date date')
            ->join('App\Entity\departure', 'd', 'WITH', 'r.departure = d')
            ->where('r.client = :client')
            ->andWhere('d.date >= :date')
            ->andWhere('r.status = 1')
            ->orderBy('date', 'ASC')
            ->setParameter('client', $user)
            ->setParameter('date', new DateTime())
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
    
    /**
     * Retourne les réservations de l'année courante payées
     */
    public function findReservationByMonthYearNow(): ?array
    {
        return $this->createQueryBuilder('r')
            ->select('count(r) as reservations, MONTH(r.createAt) as mois')
            ->where('r.createAt >= :first')
            ->andWhere('r.createAt <= :last')
            ->andWhere('r.status = 1')
            ->setParameters(
                new ArrayCollection(
                    [
                    new Parameter('first', (new DateTime("first day of January  ". date('Y')))),
                    new Parameter('last', (new DateTime("last day of December " . date('Y'))))
                    ]
                )
            )
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->getQuery()->getResult();
    }
}
