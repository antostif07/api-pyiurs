<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\ProductCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductCollectionRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['product_collection:read']],
    denormalizationContext: ['groups' => ['product_collection:write']],
    order: ['id' => 'DESC']
    // operations: [
    //     new GetCollection(),
    //     new Get(),
    //     new Post(),
    //     new Put(processor: EmployeeUpdateAttendanceProcessor::class),
    //     new Delete()
    // ]
)]
class ProductCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_collection:read'])]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_collection:read', 'product_collection:write'])]
    private ?string $segment = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_collection:read', 'product_collection:write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['product_collection:read'])]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'productCollection')]
    private Collection $products;

    #[Gedmo\Slug(fields: ["name", "id"])]
    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['product_collection:read', 'product_collection:write'])]
    #[ApiProperty(identifier: true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSegment(): ?string
    {
        return $this->segment;
    }

    public function setSegment(string $segment): static
    {
        $this->segment = $segment;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setProductCollection($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getProductCollection() === $this) {
                $product->setProductCollection(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    #[Groups('product_collection:read')] // <- MAGIC IS HERE, you can set a group on a method.
    public function getFirstProduct(): ?Product
    {
        return $this->products[0];
    }
}
