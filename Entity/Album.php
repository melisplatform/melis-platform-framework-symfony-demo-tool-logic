<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;


#[ORM\Table(name: "melis_demo_album")]
#[ORM\Entity(repositoryClass: "MelisPlatformFrameworkSymfonyDemoToolLogic\Repository\AlbumRepository")]
class Album
{

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private $alb_id;

    #[ORM\Column(type: "string", length: 255)]
    private $alb_name;

    #[ORM\Column(type: "string", length: 255)]
    private $alb_song_num;


    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $alb_date;


    public function getAlbId(): ?int
    {
        return $this->alb_id;
    }

    public function getAlbName(): ?string
    {
        return $this->alb_name;
    }

    public function setAlbName(?string $alb_name): self
    {
        $this->alb_name = $alb_name;

        return $this;
    }

    public function getAlbSongNum(): ?string
    {
        return $this->alb_song_num;
    }

    public function setAlbSongNum(?string $alb_song_num): self
    {
        $this->alb_song_num = $alb_song_num;

        return $this;
    }

    public function getAlbDate(): ?string
    {
        return $this->alb_date;
    }

    public function setAlbDate(?string $alb_date): self
    {
        $this->alb_date = $alb_date;

        return $this;
    }
}
