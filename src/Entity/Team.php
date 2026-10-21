<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $shortName = null;

    #[ORM\Column(length: 255)]
    private ?string $tla = null;

    #[ORM\Column(length: 255)]
    private ?string $crestUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $venue = null;

    #[ORM\Column]
    private ?int $founded = null;

    #[ORM\Column(length: 255)]
    private ?string $websiteUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $clubColors = null;

    #[ORM\Column(unique: true)]
    private ?int $externalId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): static
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getTla(): ?string
    {
        return $this->tla;
    }

    public function setTla(string $tla): static
    {
        $this->tla = $tla;

        return $this;
    }

    public function getCrestUrl(): ?string
    {
        return $this->crestUrl;
    }

    public function setCrestUrl(string $crestUrl): static
    {
        $this->crestUrl = $crestUrl;

        return $this;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }

    public function setVenue(string $venue): static
    {
        $this->venue = $venue;

        return $this;
    }

    public function getFounded(): ?int
    {
        return $this->founded;
    }

    public function setFounded(int $founded): static
    {
        $this->founded = $founded;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(string $websiteUrl): static
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getClubColors(): ?string
    {
        return $this->clubColors;
    }

    public function setClubColors(string $clubColors): static
    {
        $this->clubColors = $clubColors;

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }
}
