<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @ORM\Table("api_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "user_show",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "users",
 *     href = @Hateoas\Route(
 *          "user_list",
 *          absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "add",
 *     href = @Hateoas\Route(
 *          "user_add",
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_ADMIN']))"
 *      )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *          "user_delete",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_ADMIN']))"
 *      )
 * )
 * @Hateoas\Relation(
 *     "disable-enable-account",
 *     href = @Hateoas\Route(
 *          "user_switch_active",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_SUPER_ADMIN']))"
 *      )
 * )
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=35, unique=true)
     * @Assert\NotBlank()
     * @Expose()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     * @Assert\NotBlank()
     * @Expose()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=190)
     * @Expose()
     */
    private $roles;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Expose()
     */
    private $isActive;

    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    public function getRoles()
    {
        return array($this->roles);
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param bool $active
     */
    public function setActive($active): void
    {
        $this->isActive = $active;
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    /**
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * @return null|string
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
}
