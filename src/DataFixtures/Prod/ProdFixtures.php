<?php

namespace App\DataFixtures\Prod;

use App\Entity\Departure;
use App\Entity\Location;
use App\Entity\Picture;
use App\Entity\Reservation;
use App\Entity\Returned;
use App\Entity\User;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use ZipArchive;

class ProdFixtures extends Fixture
{
       
    /**
     * Créer un vol de départ
     */
    public function createDeparture(
        Location $destination,
        ObjectManager $manager,
        int $reference,
        DateTime $date
    ): void {
        $limit = rand(2, 6);
        for ($a = 1; $a <= $limit; $a++) {
            $date = new DateTime($date->format('d-m-Y').' '.rand(1, 23).':00');
            
            $departure = (new Departure)
                ->setReference("LU".($reference+$a))
                ->setDate($date)
                ->setSeat(rand(100, 450))
                ->setRocket('FALCON'.rand(1, 50))
                ->setDuration(rand(10, 25))
                ->setPrice(rand(400, 5000))
                ->setDestination($destination);

            $manager->persist($departure);
        }
    }
    
    /**
     * Créer un vol de retour
     */
    public function createReturn(
        Location $destination,
        ObjectManager $manager,
        int $reference,
        DateTime $date
    ): void {
        $limit = rand(2, 6);

        for ($a = 1; $a <= $limit; $a++) {
            $date = new DateTime($date->format('d-m-Y').' '.rand(1, 23).':00');

            $returned = (new Returned)
                ->setReference("LU".($reference+$a))
                ->setDate($date)
                ->setSeat(rand(100, 450))
                ->setRocket('FALCON'.rand(1, 50))
                ->setDuration(rand(10, 25))
                ->setPrice(rand(400, 5000))
                ->setFfrom($destination);

            $manager->persist($returned);
        }
    }
    
    /**
     * Créer une réservation
     */
    public function createReservation(
        User $client,
        Array $departure,
        Array $returned,
        ObjectManager $manager
    ): void {
        $reservation = (new Reservation)
            ->setReference(uniqid('LUXSPACE'))
            ->setClient($client)
            ->setDeparture($departure[0])
            ->setReturned($returned[20])
            ->setStatus(2)
            ->setDeparturePrice($departure[0]->getPrice())
            ->setReturnPrice($returned[20]->getPrice());

        $manager->persist($reservation);
    }
    
    /**
     * Créer une destination
     */
    public function createDestination(
        string $title,
        string $shortDescription,
        string $description,
        ObjectManager $manager
    ) {
        $destination = (new Location)
            ->setTitle($title)
            ->setShortDescription($shortDescription)
            ->setDescription($description)
            ->setCreatedAt(new DateTime())
            ->setPictureHeader("small_".strtolower(str_replace("é", "e", $title)).".jpg")
            ->addPicture((new Picture())->setFilename(strtolower(str_replace("é", "e", $title)).'.jpg'))
            ->setSlug($title);
        $manager->persist($destination);
        return $destination;
    }
    
    public function load(ObjectManager $manager): void
    {
        $mars = $this->createDestination(
            'Mars',
            'Mars est la quatrième planète par ordre croissant de la distance au Soleil et la deuxième par ordre croissant de la taille et de la masse.',
            "C’est une planète tellurique, comme le sont Mercure, Vénus et la Terre, environ dix fois moins massive que la Terre mais dix fois plus massive que la Lune. Sa topographie présente des analogies aussi bien avec la Lune, à travers ses cratères et ses bassins d'impact, qu'avec la Terre, avec des formations d'origine tectonique et climatique telles que des volcans, des rifts, des vallées, des mesas, des champs de dunes et des calottes polaires. Le plus haut volcan du Système solaire, Olympus Mons (qui est un volcan bouclier), et le plus grand canyon, Valles Marineris, se trouvent sur Mars.",
            $manager
        );

        $lune = $this->createDestination(
            'Lune',
            "La Lune est l'unique satellite naturel permanent de la planète Terre. Il s'agit du cinquième plus grand satellite naturel du Système solaire.",
            "La Lune est en rotation avec la Terre, lui montrant donc constamment la même face. Celle-ci, appelée face visible, est marquée par des mers lunaires volcaniques sombres qui remplissent les espaces entre les hautes terres claires — certaines atteignant 9 km d'altitude — et ses cratères d'impact proéminents. Réciproquement, elle possède une face cachée, qui présente moins de mers mais beaucoup plus de cratères, dont le bassin Pôle Sud-Aitken, le plus grand du satellite et l'un des plus grands du Système solaire par son diamètre de 2 500 km. Elle est dépourvue d'atmosphère dense et de champ magnétique. Son influence gravitationnelle sur la Terre produit les marées océaniques, les marées terrestres, un léger allongement de la durée du jour et la stabilisation de l'inclinaison de l'axe terrestre.",
            $manager
        );

        $jupiter = $this->createDestination(
            'Jupiter',
            "Jupiter est une planète géante gazeuse. Il s'agit de la plus grosse planète du Système solaire, plus volumineuse et massive que toutes les autres planètes réunies.",
            "Visible à l'œil nu dans le ciel nocturne, Jupiter est habituellement le quatrième objet le plus brillant de la voûte céleste, après le Soleil, la Lune et Vénus6. Parfois, Mars apparaît plus lumineuse que Jupiter et, de temps en temps, Jupiter apparaît plus lumineuse que Vénus7. Jupiter était au périhélie le 17 mars 20118 et à l'aphélie le 17 février 20179. Comme sur les autres planètes gazeuses, des vents violents, de près de 600 km/h, parcourent les couches supérieures de la planète. La Grande Tache rouge est un anticyclone, une zone de surpression observée depuis au moins le XVIIe siècle. Trois fois plus grande que la Terre au début du XXe siècle, elle a rétréci pour devenir de taille comparable un siècle plus tard.",
            $manager
        );

        $venus = $this->createDestination(
            'Vénus',
            "Vénus est la deuxième planète du Système solaire par ordre d'éloignement au Soleil, et la sixième plus grosse aussi bien par la masse que le diamètre.",
            "Vénus orbite autour du Soleil tous les 224,7 jours terrestres. Avec une période de rotation de 243 jours terrestres, il lui faut plus de temps pour tourner autour de son axe que toute autre planète du Système solaire. Comme Uranus, elle possède une rotation rétrograde et tourne dans le sens opposé à celui des autres planètes : le soleil s'y lève à l'ouest et se couche à l'est. Vénus possède l'orbite la plus circulaire des planètes du Système solaire avec une excentricité orbitale presque nulle et, du fait de sa lente rotation, est quasiment sphérique (aplatissement considéré comme nul).",
            $manager
        );

        $neptune = $this->createDestination(
            'Neptune',
            "Neptune est la huitième planète par ordre d'éloignement au Soleil et la plus éloignée connue du Système solaire. Elle orbite autour du Soleil à une distance d'environ 30,1 UA.",
            "Neptune est la huitième planète par ordre d'éloignement au Soleil et la plus éloignée connue du Système solaire. Elle orbite autour du Soleil à une distance d'environ 30,1 UA (4,5 milliards de kilomètres), avec une excentricité orbitale moitié moindre que celle de la Terre et une période de révolution de 164,79 ans. Il s'agit de la troisième planète la plus massive du Système solaire et de la quatrième plus grande par la taille — un peu plus massive mais un peu plus petite qu'Uranus.",
            $manager
        );

        $mercure = $this->createDestination(
            'Mercure',
            "Mercure est la planète la plus proche du Soleil et la moins massive du Système solaire. Son éloignement au Soleil est compris entre 46 et 70 millions de kilomètres).",
            "Mercure est la planète la plus proche du Soleil et la moins massive du Système solaireN 1. Son éloignement au Soleil est compris entre 46 et 70 millions de kilomètres, ce qui correspond à une excentricité orbitale de 0,2 — plus de douze fois supérieure à celle de la Terre, et de loin la plus élevée pour une planète du Système solaire. Elle est visible à l'œil nu depuis la Terre avec un diamètre apparent de 4,5 à 13 secondes d'arc.",
            $manager
        );

        $uranus = $this->createDestination(
            'Uranus',
            "Uranus est la septième planète du Système solaire par ordre d'éloignement au Soleil. Elle orbite autour de celui-ci à une distance d'environ 19,2 unités astronomiques.",
            "Uranus est la septième planète du Système solaire par ordre d'éloignement au Soleil. Elle orbite autour de celui-ci à une distance d'environ 19,2 unités astronomiques (2,87 milliards de kilomètres), avec une période de révolution de 84,05 années terrestres. Il s'agit de la quatrième planète la plus massive du Système solaire et de la troisième plus grande par la taille. Elle est la première planète découverte à l’époque moderne avec un télescope et non connue depuis l'Antiquité.",
            $manager
        );

        $saturn = $this->createDestination(
            'Saturn',
            "Saturne est la sixième planète du Système solaire par ordre d'éloignement au Soleil, et la deuxième plus grande par la taille et la masse après Jupiter.",
            "Saturne est la sixième planète du Système solaire par ordre d'éloignement au Soleil, et la deuxième plus grande par la taille et la masse après Jupiter, qui est comme elle une planète géante gazeuse. Son rayon moyen de 58 232 km est environ neuf fois et demi celui de la Terre et sa masse de 568,46 × 1024 kg est 95 fois plus grande. Orbitant en moyenne à environ 1,4 milliard de kilomètres du Soleil (9,5 unités astronomiques). ",
            $manager
        );

        $admin = (new User)
            ->setEmail('admin@admin.be')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$flIblTtYgRo8pmzFVZNxCg$ERQmU8E9+lR9qFeMXVrBCP7EU/1F+ggQ7mdOUO43lHA')// %Password00
            ->setLastname('UnNom')
            ->setFirstname('UnPrenom')
            ->setBirthday(new DateTime('14-07-1988'))
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt(new DateTime())
            ->setAddress('Rue de Luxembourg')
            ->setPostalCode('0000')
            ->setCountry('Luxembourg')
            ->setCity('LuxembourgCity')
            ->setPhone(00000000);
        $manager->persist($admin);
        $manager->flush();

        $now = new DateTime();
        $nextYear = (new DateTime())->add(new DateInterval('P1Y'));

        $ref=1000;
        
        for ($i = 1; $now < $nextYear; $i+5) {
            $now->add(new DateInterval('P5D'));
            $this->createDeparture($mars, $manager, $ref+=9, $now);
            $this->createDeparture($lune, $manager, $ref+=9, $now);
            $this->createDeparture($jupiter, $manager, $ref+=9, $now);
            $this->createDeparture($venus, $manager, $ref+=9, $now);
            $this->createDeparture($mercure, $manager, $ref+=9, $now);
            $this->createDeparture($uranus, $manager, $ref+=9, $now);
            $this->createDeparture($saturn, $manager, $ref+=9, $now);
            $this->createDeparture($neptune, $manager, $ref+=9, $now);
            
            $this->createReturn($mars, $manager, $ref+=9, $now);
            $this->createReturn($lune, $manager, $ref+=9, $now);
            $this->createReturn($jupiter, $manager, $ref+=9, $now);
            $this->createReturn($venus, $manager, $ref+=9, $now);
            $this->createReturn($mercure, $manager, $ref+=9, $now);
            $this->createReturn($uranus, $manager, $ref+=9, $now);
            $this->createReturn($saturn, $manager, $ref+=9, $now);
            $this->createReturn($neptune, $manager, $ref+=9, $now);

            $manager->flush();
        }

        $destinationRepo = $manager->getRepository(Location::class);
        $departuresRepo = $manager->getRepository(Departure::class);
        $returnedsRepo = $manager->getRepository(Returned::class);

        $mars = $destinationRepo->findOneBy(['title' => 'Mars']);
        $dMars = $departuresRepo->findBy(['destination' => $mars]);
        $rMars = $returnedsRepo->findBy(['ffrom' => $mars]);

        $lune = $destinationRepo->findOneBy(['title' => 'Lune']);
        $dLune = $departuresRepo->findBy(['destination' => $lune]);
        $rLune = $returnedsRepo->findBy(['ffrom' => $lune]);

        $saturn = $destinationRepo->findOneBy(['title' => 'Saturn']);
        $dSaturn = $departuresRepo->findBy(['destination' => $saturn]);
        $rSaturn = $returnedsRepo->findBy(['ffrom' => $saturn]);

        $uranus = $destinationRepo->findOneBy(['title' => 'Uranus']);
        $dUranus = $departuresRepo->findBy(['destination' => $uranus]);
        $rUranus = $returnedsRepo->findBy(['ffrom' => $uranus]);

        $this->createReservation($admin, $dMars, $rMars, $manager);
        $this->createReservation($admin, $dLune, $rLune, $manager);
        $this->createReservation($admin, $dSaturn, $rSaturn, $manager);
        $this->createReservation($admin, $dUranus, $rUranus, $manager);
        $manager->flush();

        $zip = new ZipArchive;
        if ($zip->open(dirname(__DIR__, 2).'/DataFixtures/Prod/location.zip') === true) {
            $zip->extractTo(dirname(__DIR__, 3).'/public/images/');
            $zip->close();
            echo 'pictures ok';
        } else {
            throw new Exception("Impossible d'extraire l'archive images.zip dans le dossier location");
        }
    }
}
