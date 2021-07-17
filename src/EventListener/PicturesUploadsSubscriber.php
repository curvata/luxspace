<?php

namespace App\EventListener;

use App\Entity\Location;
use App\Entity\Picture;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Intervention\Image\ImageManager;

class PicturesUploadsSubscriber implements EventSubscriber
{
    const UPLOAD_PATH_PROPERTIES = 'images/location/';
    
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }
    
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->uploadsPictures($args);
    }
    
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->uploadsPictures($args);
    }
    
    /**
     * Téléchargement des images pour la galerie et l'en-tête
     */
    public function uploadsPictures(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Images pour la galerie
        if ($entity instanceof Picture) {
            /** @var UploadFile */
            $uploadPicture = $entity->getPictureFile();

            if ($uploadPicture) {
                $name = $this->generateNewImageName($uploadPicture->getClientOriginalName());
                $entity->setFileName($name);
                $uploadPicture->move(self::UPLOAD_PATH_PROPERTIES, $entity->getFileName());
                $this->generateThumbmail($entity->getFileName());
            }
        }

        // Image d'en-tête
        if ($entity instanceof Location) {
            
            /** @var UploadFile */
            $uploadPicture = $entity->getHeaderPictureFile();

            if ($uploadPicture) {
                if ($entity->getPictureHeader()) {
                    $oldFile = self::UPLOAD_PATH_PROPERTIES . $entity->getPictureHeader();
                    if (file_exists($oldFile) && $entity->getPictureHeader() != 'placeholder.png') {
                        unlink($oldFile);
                    }
                }

                $name = $this->generateNewImageName($uploadPicture->getClientOriginalName());
                $entity->setPictureHeader('small_'.$name);
                $uploadPicture->move(self::UPLOAD_PATH_PROPERTIES, $name);
                $this->generateThumbmail($name);
                unlink(self::UPLOAD_PATH_PROPERTIES.$name);
            } else {
                if ($entity->getPictureHeader() === null) {
                    $entity->setPictureHeader('placeholder.png');
                }
            }
        }
    }
        
    public function generateNewImageName(string $name): string
    {
        $originalName = substr($name, 0, strpos($name, '.'));
        $extension = substr($name, strpos($name, '.'));
        return uniqid($originalName).$extension;
    }
    
    public function generateThumbmail(string $fileName): void
    {
        $manager = new ImageManager(array('driver' => 'Gd'));
        $image = $manager->make(self::UPLOAD_PATH_PROPERTIES.$fileName)->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
        });
        $image->save(self::UPLOAD_PATH_PROPERTIES.'small_'.$fileName);
    }
}
