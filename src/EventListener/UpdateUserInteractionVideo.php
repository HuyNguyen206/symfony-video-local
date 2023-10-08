<?php

namespace App\EventListener;

use App\Entity\UserInteractiveVideo;
use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::onFlush)]
class UpdateUserInteractionVideo
{
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof UserInteractiveVideo) {
                foreach ($uow->getEntityChangeSet($entity) as $keyField => $field) {
                    if ($keyField === 'type') {
                        if (($newValue = $field['1']) == ($oldValue = $field[0])) {
                            return;
                        }
                        $video = $entity->getVideo();

                        if ($oldValue == true && $newValue == false) {
                            $video->setLikeCount($video->getLikeCount() - 1);
                            $video->setDislikeCount($video->getDislikeCount() + 1);
                        }

                        if ($oldValue == false && $newValue == true) {
                            $video->setLikeCount($video->getLikeCount() + 1);
                            $video->setDislikeCount($video->getDislikeCount() - 1);
                        }
                    }

                    // place here all the setters
                    $em->persist($video);
                    $classMetadata = $em->getClassMetadata(Video::class);
                    $uow->computeChangeSet($classMetadata, $video);
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
//        return [
//            'doctrine.event_subscriber' =>  [
//                ['updateInteractionCount', 1]
//            ],
//        ];

        return [
            'onFlush'
        ];
    }
}
