<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 * @ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "client_show",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "clients",
 *     href = @Hateoas\Route(
 *          "client_list",
 *          absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "add-client-admin",
 *     href = @Hateoas\Route(
 *          "admin_add",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_SUPER_ADMIN']))"
 *      )
 * )
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Expose()
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     * @Expose()
     */
    private $leader;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="client", orphanRemoval=true)
     * @Expose()
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
