<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['product:read']],
    denormalizationContext: ['groups' => ['product:write']],
    // operations: [
    //     new GetCollection(),
    //     new Get(),
    //     new Post(),
    //     new Put(processor: EmployeeUpdateAttendanceProcessor::class),
    //     new Delete()
    // ]
)]
#[ApiFilter(SearchFilter::class, properties: ['productCollection.slug' => 'iexact'])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_collection:read', 'product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_collection:read', 'product:write', 'product:read'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_collection:read', 'product:write', 'product:read'])]
    private ?string $taille = null;

    #[ORM\Column]
    #[Groups(['product_collection:read', 'product:write', 'product:read'])]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_collection:read', 'product:write', 'product:read'])]
    private ?string $color = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups(['product:write', 'product:read'])]
    private ?ProductCollection $productCollection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getProductCollection(): ?ProductCollection
    {
        return $this->productCollection;
    }

    public function setProductCollection(?ProductCollection $productCollection): static
    {
        $this->productCollection = $productCollection;

        return $this;
    }
}
