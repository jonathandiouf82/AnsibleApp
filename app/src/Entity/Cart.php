<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ApiResource(
    collectionOperations: ['get','post'],
    itemOperations: [
        'get', 'patch', 'delete'
    ],
)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $totalPrice;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    private $productList;

    public function __construct()
    {
        $this->productList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductList(): Collection
    {
        return $this->productList;
    }

    public function addProductList(Product $productList): self
    {
        if (!$this->productList->contains($productList)) {
            $this->productList[] = $productList;
        }

        return $this;
    }

    public function removeProductList(Product $productList): self
    {
        $this->productList->removeElement($productList);

        return $this;
    }
}
