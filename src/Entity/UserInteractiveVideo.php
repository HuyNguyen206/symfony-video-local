<?php

namespace App\Entity;

use App\Repository\UserInteractiveVideoRepository;
use App\Traits\Timestamp;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserInteractiveVideoRepository::class)]
#[UniqueEntity(fields: ['user_id', 'video_id', 'type'])]
#[ORM\HasLifecycleCallbacks]
class UserInteractiveVideo
{
    use Timestamp;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userInteractiveVideos')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column()]
    private ?bool $type = null;

    #[ORM\ManyToOne(inversedBy: 'userInteractiveVideos')]
    private ?Video $video = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function isLiked()
    {
        return $this->isType();
    }

    public function isDisliked()
    {
        return !$this->isType();
    }

    #[ORM\PrePersist]
    public function addUserInteraction(PrePersistEventArgs $eventArgs)
    {
        $interaction = $eventArgs->getObject();
        $isLiked = $interaction->isLiked();
        $video = $interaction->getVideo();
        if ($isLiked) {
            $video->setLikeCount($video->getLikeCount() + 1);
        } else {
            $video->setDislikeCount($video->getDislikeCount() + 1);
        }
        $eventArgs->getObjectManager()->persist($video);
    }
}
