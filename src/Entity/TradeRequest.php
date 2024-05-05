<?php

namespace App\Entity;

use App\Repository\TradeRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TradeRequestRepository::class)]
class TradeRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDatetime = null;

    #[ORM\ManyToOne(inversedBy: 'tradeRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $author = null;

    #[ORM\Column]
    private ?bool $status = false;


    #[ORM\ManyToOne(inversedBy: 'tradeRequests')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SuperPower $power = null;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'Trade')]
    private Collection $conversations;

    public function __construct()
    {
        $this->creationDatetime = new \DateTime('now', new \DateTimeZone('Indian/Reunion'));
        $this->conversations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDatetime(): ?\DateTimeInterface
    {
        return $this->creationDatetime;
    }

    public function setCreationDatetime(\DateTimeInterface $creationDatetime): static
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    public function getAuthor(): ?user
    {
        return $this->author;
    }

    public function setAuthor(?user $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPower(): ?SuperPower
    {
        return $this->power;
    }

    public function setPower(?SuperPower $power): static
    {
        $this->power = $power;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
            $conversation->setTrade($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getTrade() === $this) {
                $conversation->setTrade(null);
            }
        }

        return $this;
    }
}
