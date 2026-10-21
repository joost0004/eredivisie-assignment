<?php

namespace App\Entity;

use App\Enum\GameStatus;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $externalId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $utcDate = null;

    #[ORM\Column]
    private ?int $matchday = null;

    #[ORM\Column(enumType: GameStatus::class)]
    private ?GameStatus $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $homeTeam = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $awayTeam = null;

    #[ORM\Column(nullable: true)]
    private ?int $homeScore = null;

    #[ORM\Column(nullable: true)]
    private ?int $awayScore = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUtcDate(): ?\DateTimeImmutable
    {
        return $this->utcDate;
    }

    public function setUtcDate(\DateTimeImmutable $utcDate): static
    {
        $this->utcDate = $utcDate;

        return $this;
    }

    public function getMatchday(): ?int
    {
        return $this->matchday;
    }

    public function setMatchday(int $matchday): static
    {
        $this->matchday = $matchday;

        return $this;
    }

    public function getStatus(): ?GameStatus
    {
        return $this->status;
    }

    public function setStatus(GameStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(Team $homeTeam): static
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): ?Team
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(Team $awayTeam): static
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(?int $homeScore): static
    {
        $this->homeScore = $homeScore;

        return $this;
    }

    public function getAwayScore(): ?int
    {
        return $this->awayScore;
    }

    public function setAwayScore(?int $awayScore): static
    {
        $this->awayScore = $awayScore;

        return $this;
    }
}
