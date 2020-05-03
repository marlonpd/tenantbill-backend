<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tenant", mappedBy="owner", orphanRemoval=true)
     */
    private $tenants;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PowerRate", mappedBy="owner")
     */
    private $powerRates;

    public function __construct()
    {
        $this->tenants = new ArrayCollection();
        $this->powerRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Tenant[]
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenant(Tenant $tenant): self
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants[] = $tenant;
            $tenant->setOwner($this);
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): self
    {
        if ($this->tenants->contains($tenant)) {
            $this->tenants->removeElement($tenant);
            // set the owning side to null (unless already changed)
            if ($tenant->getOwner() === $this) {
                $tenant->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PowerRate[]
     */
    public function getPowerRates(): Collection
    {
        return $this->powerRates;
    }

    public function addPowerRate(PowerRate $powerRate): self
    {
        if (!$this->powerRates->contains($powerRate)) {
            $this->powerRates[] = $powerRate;
            $powerRate->setOwner($this);
        }

        return $this;
    }

    public function removePowerRate(PowerRate $powerRate): self
    {
        if ($this->powerRates->contains($powerRate)) {
            $this->powerRates->removeElement($powerRate);
            // set the owning side to null (unless already changed)
            if ($powerRate->getOwner() === $this) {
                $powerRate->setOwner(null);
            }
        }

        return $this;
    }
}
