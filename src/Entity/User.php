<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = ["ROLE_USER"];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthdate = null;

    /**
     * @var Collection<int, SuperPower>
     */
    #[ORM\OneToMany(targetEntity: SuperPower::class, mappedBy: 'owner')]
    private Collection $superPowers;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column]
    private ?bool $verified = false;

    /**
     * @var Collection<int, TradeRequest>
     */
    #[ORM\OneToMany(targetEntity: TradeRequest::class, mappedBy: 'author')]
    private Collection $tradeRequests;
    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'user1')]
    private Collection $conversations;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'emitter')]
    private Collection $messages;

    /**
     * @var Collection<int, Message>
     */

    public function __construct()
    {
        $this->superPowers = new ArrayCollection();
        $this->tradeRequests = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->conversations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @return Collection<int, SuperPower>
     */
    public function getSuperPowers(): Collection
    {
        return $this->superPowers;
    }

    public function addSuperPower(SuperPower $superPower): static
    {
        if (!$this->superPowers->contains($superPower)) {
            $this->superPowers->add($superPower);
            $superPower->setOwner($this);
        }

        return $this;
    }

    public function removeSuperPower(SuperPower $superPower): static
    {
        if ($this->superPowers->removeElement($superPower)) {
            // set the owning side to null (unless already changed)
            if ($superPower->getOwner() === $this) {
                $superPower->setOwner(null);
            }
        }

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): static
    {
        $this->verified = $verified;

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
            $tradeRequest->setAuthor($this);
        }

        return $this;
    }

    public function removeTradeRequest(TradeRequest $tradeRequest): static
    {
        if ($this->tradeRequests->removeElement($tradeRequest)) {
            // set the owning side to null (unless already changed)
            if ($tradeRequest->getAuthor() === $this) {
                $tradeRequest->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setEmitter($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getEmitter() === $this) {
                $message->setEmitter(null);
            }
        }

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
            $conversation->setUser1($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getUser1() === $this) {
                $conversation->setUser1(null);
            }
        }

        return $this;
    }
}
