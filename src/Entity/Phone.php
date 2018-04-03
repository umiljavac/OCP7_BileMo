<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhoneRepository")
 *
 * @ExclusionPolicy("all");
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "phone_show",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "list-phones-same-mark",
 *     href = @Hateoas\Route(
 *          "phone_list_criteria",
 *          parameters={"keyword"="expr(object.getMark())"},
 *          absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "list-all-phones",
 *     href = @Hateoas\Route(
 *          "phone_list_all",
 *          absolute=true
 *     )
 * )
 */
class Phone
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
     * @Expose()
     */
    private $mark;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Expose()
     */
    private $reference;

    /**
     * @ORM\Column(type="text")
     * @Expose()
     */
    private $description;

    /**
     * @ORM\Column(type="float", scale=2)
     * @Expose()
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
