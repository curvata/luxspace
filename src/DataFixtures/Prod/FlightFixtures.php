<?php

namespace App\DataFixtures\Prod;

use App\Entity\Departure;
use App\Entity\Location;
use App\Entity\Returned;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class FlightFixtures extends Fixture implements FixtureGroupInterface
{
    private int $ref = 1;

    private array $rockets = [
        'FALCON-9', 'FALCON HEAVY', 'STARSHIP', 'ARTEMIS I', 'ATLAS V',
        'VULCAN', 'NEW GLENN', 'ARIANE 6', 'SOYUZ-2', 'LONG MARCH 5',
    ];

    public static function getGroups(): array
    {
        return ['flights'];
    }

    private function nextRef(): string
    {
        return 'LU' . str_pad($this->ref++, 6, '0', STR_PAD_LEFT);
    }

    private function randomRocket(): string
    {
        return $this->rockets[array_rand($this->rockets)];
    }

    private function createDeparture(Location $destination, ObjectManager $manager, DateTime $baseDate): void
    {
        $flightsPerDay = rand(2, 5);
        $usedHours = [];

        for ($i = 0; $i < $flightsPerDay; $i++) {
            do {
                $hour = rand(1, 23);
            } while (in_array($hour, $usedHours));
            $usedHours[] = $hour;

            $date = new DateTime($baseDate->format('Y-m-d') . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00');

            $departure = (new Departure())
                ->setReference($this->nextRef())
                ->setDate($date)
                ->setSeat(rand(100, 450))
                ->setRocket($this->randomRocket())
                ->setDuration(rand(10, 25))
                ->setPrice(rand(400, 5000))
                ->setDestination($destination);

            $manager->persist($departure);
        }
    }

    private function createReturn(Location $destination, ObjectManager $manager, DateTime $baseDate): void
    {
        $flightsPerDay = rand(2, 5);
        $usedHours = [];

        for ($i = 0; $i < $flightsPerDay; $i++) {
            do {
                $hour = rand(1, 23);
            } while (in_array($hour, $usedHours));
            $usedHours[] = $hour;

            $date = new DateTime($baseDate->format('Y-m-d') . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00');

            $returned = (new Returned())
                ->setReference($this->nextRef())
                ->setDate($date)
                ->setSeat(rand(100, 450))
                ->setRocket($this->randomRocket())
                ->setDuration(rand(10, 25))
                ->setPrice(rand(400, 5000))
                ->setFfrom($destination);

            $manager->persist($returned);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $destinations = $manager->getRepository(Location::class)->findAll();

        if (empty($destinations)) {
            echo "Aucune destination trouvée, FlightFixtures ignorée.\n";
            return;
        }

        $now    = new DateTime('today');
        $end    = (new DateTime('today'))->add(new DateInterval('P10Y'));
        $batch  = 0;

        while ($now <= $end) {
            foreach ($destinations as $destination) {
                $this->createDeparture($destination, $manager, clone $now);
                $this->createReturn($destination, $manager, clone $now);
            }

            $now->add(new DateInterval('P5D'));
            $batch++;

            // Flush tous les 50 cycles pour éviter les problèmes mémoire
            if ($batch % 50 === 0) {
                $manager->flush();
                $manager->clear(Departure::class);
                $manager->clear(Returned::class);
                echo "Vols générés jusqu'au : " . $now->format('d/m/Y') . "\n";
            }
        }

        $manager->flush();
        echo "Terminé — " . ($this->ref - 1) . " vols créés sur 10 ans.\n";
    }
}
