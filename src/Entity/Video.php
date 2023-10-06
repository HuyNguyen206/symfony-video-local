<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use App\Traits\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\Table(name: 'videos')]
#[ORM\Index(fields: ['title'], name: 'title_idx')]
class Video
{
    use Timestamp;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: Comment::class, cascade: ['persist'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: UserInteractiveVideo::class)]
    private Collection $userInteractiveVideos;

    protected const VIMEO_VIDEO_FOR_USER_NOT_LOGGED_IN = 'https://player.vimeo.com/video/non-exist';
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->userInteractiveVideos = new ArrayCollection();
    }

    public function getVimeoId(?User $user)
    {
        if ($user) {
            return $this->path;
        }

        return self::VIMEO_VIDEO_FOR_USER_NOT_LOGGED_IN;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setVideo($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getVideo() === $this) {
                $comment->setVideo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserInteractiveVideo>
     */
    public function getUserInteractiveVideos(): Collection
    {
        return $this->userInteractiveVideos;
    }

    public function addUserInteractiveVideo(UserInteractiveVideo $userInteractiveVideo): static
    {
        if (!$this->userInteractiveVideos->contains($userInteractiveVideo)) {
            $this->userInteractiveVideos->add($userInteractiveVideo);
            $userInteractiveVideo->setVideo($this);
        }

        return $this;
    }

    public function removeUserInteractiveVideo(UserInteractiveVideo $userInteractiveVideo): static
    {
        if ($this->userInteractiveVideos->removeElement($userInteractiveVideo)) {
            // set the owning side to null (unless already changed)
            if ($userInteractiveVideo->getVideo() === $this) {
                $userInteractiveVideo->setVideo(null);
            }
        }

        return $this;
    }
}
