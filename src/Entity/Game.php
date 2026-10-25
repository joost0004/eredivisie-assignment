<?php

namespace App\Entity;

use App\Enum\GameResult;
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
    private ?int $seasonExternalId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $kickoffUtc = null;

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

    public function getSeasonExternalId(): ?int
    {
        return $this->seasonExternalId;
    }

    public function setSeasonExternalId(int $seasonExternalId): static
    {
        $this->seasonExternalId = $seasonExternalId;

        return $this;
    }

    public function getKickoffUtc(): ?\DateTimeImmutable
    {
        // the column stores UTC without a zone
        return $this->kickoffUtc
            ? new \DateTimeImmutable($this->kickoffUtc->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'))
            : null;
    }

    public function setKickoffUtc(\DateTimeImmutable $kickoffUtc): static
    {
        $kickoffUtc = $kickoffUtc->setTimezone(new \DateTimeZone('UTC'));

        // doctrine compares dates by instance
        if ($this->kickoffUtc?->format('Y-m-d H:i:s') !== $kickoffUtc->format('Y-m-d H:i:s')) {
            $this->kickoffUtc = $kickoffUtc;
        }

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

    public function opponentOf(Team $team): Team
    {
        return $team === $this->homeTeam ? $this->awayTeam : $this->homeTeam;
    }

    public function resultFor(Team $team): ?GameResult
    {
        if (!$this->status->isFinished()) {
            return null;
        }

        $isHome = $team === $this->homeTeam;
        $for = $isHome ? $this->homeScore : $this->awayScore;
        $against = $isHome ? $this->awayScore : $this->homeScore;

        return GameResult::fromScore($for, $against);
    }
}
