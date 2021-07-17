<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
    
    /**
     * Retourne les nouveaux utilisateurs de l'année courante
     */
    public function findUserByMonthYearNow(): ?array
    {
        return $this->createQueryBuilder('u')
            ->select('count(u) as users, MONTH(u.createdAt) as mois')
            ->where('u.createdAt >= :first')
            ->andWhere('u.createdAt <= :last')
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
