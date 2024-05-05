<?php

namespace App\Entity;

use App\Repository\SuperPowerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuperPowerRepository::class)]
class SuperPower
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'superPowers')]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, TradeRequest>
     */
    #[ORM\OneToMany(targetEntity: TradeRequest::class, mappedBy: 'power')]
    private Collection $tradeRequests;

    public function __construct()
    {
        $this->tradeRequests = new ArrayCollection();
    }

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, TradeRequest>
     */
    public function getTradeRequests(): Collection
    {
        return $this->tradeRequests;
    }

    public function addTradeRequest(TradeRequest $tradeRequest): static
    {
        if (!$this->tradeRequests->contains($tradeRequest)) {
            $this->tradeRequests->add($tradeRequest);
            $tradeRequest->setPower($this);
        }

        return $this;
    }

    public function removeTradeRequest(TradeRequest $tradeRequest): static
    {
        if ($this->tradeRequests->removeElement($tradeRequest)) {
            // set the owning side to null (unless already changed)
            if ($tradeRequest->getPower() === $this) {
                $tradeRequest->setPower(null);
            }
        }

        return $this;
    }
}
