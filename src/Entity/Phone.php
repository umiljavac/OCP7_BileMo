<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhoneRepository")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "phone_show",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"list", "detail", "mark"}
 *      )
 * )
 * @Hateoas\Relation(
 *     "list-phones-same-mark",
 *     href = @Hateoas\Route(
 *          "phone_list_mark",
 *          parameters={"mark"="expr(object.getMark())"},
 *          absolute=true
 *     ),
 *      exclusion = @Hateoas\Exclusion(
 *          groups={"list", "detail"}
 *      )
 * )
 * @Hateoas\Relation(
 *     "list-all-phones",
 *     href = @Hateoas\Route(
 *          "phone_list",
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"list", "detail"}
 *      )
 * )
 * @Hateoas\Relation(
 *     "phone-partial-update",
 *     href = @Hateoas\Route(
 *          "phone_update_patch",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_SUPER_ADMIN']))",
 *          groups={"list", "detail"}
 *      )
 * )
 * @Hateoas\Relation(
 *     "phone-full-update",
 *     href = @Hateoas\Route(
 *          "phone_update_put",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_SUPER_ADMIN']))",
 *           groups= {"list", "detail"}
 *      )
 * )
 * @Hateoas\Relation(
 *     "phone-delete",
 *     href = @Hateoas\Route(
 *          "phone_delete",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted(['ROLE_SUPER_ADMIN']))",
 *           groups= {"list", "detail"}
 *      )
 * )
 */
class Phone
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list","detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Groups({"list", "detail"})
     */
    private $mark;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     * @Groups({"list", "detail", "mark"})
     */
    private $reference;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"detail"})
     */
    private $description;

    /**
     * @ORM\Column(type="float", scale=2)
     * @Assert\NotBlank()
     * @Groups({"list", "detail", "mark"})
     */
    private $price;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * @param mixed $mark
     */
    public function setMark($mark): void
    {
        $this->mark = $mark;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }
}
