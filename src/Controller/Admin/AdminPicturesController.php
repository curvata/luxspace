<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminPicturesController extends AbstractController
{
    private EntityManagerInterface $manager;
    
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    /**
     * Retourne les miniatures de la destination
     */
    #[Route('/admin/pictures/location/{id}', name: 'admin.location.show.pictures', requirements: ["id" => "\d+"], methods: ['GET'])]
    public function getPictures(Location $location): JsonResponse
    {
        $pictures = $location->getPictures()->getValues();

        $array = [];

        foreach ($pictures as $key => $picture) {
            $array[$key][] = $picture->getId();
            $array[$key][] = $picture->getSmallPicture();
        }

        return new JsonResponse($array);
    }

    /**
     * Supprimer l'image de la destination concernée
     */
    #[Route('/admin/pictures/location/delete/{id}', name: 'admin.location.remove.pictures', requirements: ["id" => "\d+"], methods: ["DELETE"])]
    public function removePicture(Picture $picture): JsonResponse
    {
        if ($picture->getFilename()) {
            unlink('images/location/'.$picture->getFilename());
            unlink('images/location/small_'.$picture->getFilename());
            $this->manager->remove($picture);
            $this->manager->flush();
        }

        return new JsonResponse(null, 200);
    }
}
