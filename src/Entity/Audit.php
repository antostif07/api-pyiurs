<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AuditRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['audit:read']],
    denormalizationContext: ['groups' => ['audit:write']],
    order: ['createdAt' => 'DESC']
)]
class Audit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['audit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'audits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?Assignment $assignment = null;

    #[ORM\Column(length: 255)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?string $segment = null;

    #[ORM\Column(length: 255)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?string $categories = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?MediaObject $baseFile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?string $status = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?MediaObject $resultFile = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?MediaObject $totalBaseFile = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(?Assignment $assignment): static
    {
        $this->assignment = $assignment;

        return $this;
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

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getBaseFile(): ?MediaObject
    {
        return $this->baseFile;
    }

    public function setBaseFile(MediaObject $baseFile): static
    {
        $this->baseFile = $baseFile;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getResultFile(): ?MediaObject
    {
        return $this->resultFile;
    }

    public function setResultFile(?MediaObject $resultFile): static
    {
        $this->resultFile = $resultFile;

        return $this;
    }

    public function getTotalBaseFile(): ?MediaObject
    {
        return $this->totalBaseFile;
    }

    public function setTotalBaseFile(?MediaObject $totalBaseFile): static
    {
        $this->totalBaseFile = $totalBaseFile;

        return $this;
    }

    #[Groups(['audit:read'])]
    public function getAssignmentName(): string
    {
        return $this->getAssignment()->getName();
    }
}
