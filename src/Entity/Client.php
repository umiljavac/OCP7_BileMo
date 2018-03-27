<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    private $leader;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="client", orphanRemoval=true)
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return User
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * @param User $leader
     */
    public function setLeader($leader): void
    {
        $this->leader = $leader;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     */
    public function addUsers(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);
    }
}
