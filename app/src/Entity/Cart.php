<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
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

    #[ORM\OneToOne(inversedBy: 'cart', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    public function __construct()
    {
        $this->productList = new ArrayCollection();
    }
    
    public function reset(): self
    {
        $this->productList = new ArrayCollection();
        $this->totalPrice = 0;

        return $this;
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
    public function getProductList(): array
    {
        return $this->productList->toArray();
    }

    public function addProductList(Product $productList): self
    {
        if (!$this->productList->contains($productList)) {
            $this->productList[] = $productList;
            $this->setTotalPrice($this->getTotalPrice() + $productList->getPrice());
        }

        return $this;
    }

    public function removeProductList(Product $productList): self
    {
        $this->productList->removeElement($productList);
        $this->setTotalPrice($this->getTotalPrice() - $productList->getPrice());
        if ($this->getTotalPrice() < 0) {
            $this->setTotalPrice(0);
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
