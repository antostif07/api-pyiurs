<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\EmployeePrimeRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    normalizationContext: ['groups' => ['employee-prime:read']],
    denormalizationContext: ['groups' => ['employee-prime:write']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'employee' => 'exact',
])]
#[ORM\Entity(repositoryClass: EmployeePrimeRepository::class)]
class EmployeePrime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['employee-prime:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employeePrimes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employee-prime:read', 'employee-prime:write'])]
    private ?Employee $employee = null;

    #[ORM\Column(length: 255)]
    #[Groups(['employee-prime:read', 'employee-prime:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['employee-prime:read', 'employee-prime:write'])]
    private ?float $amount = null;

    #[ORM\Column(length: 7)]
    #[Groups(['employee-prime:read', 'employee-prime:write'])]
    private ?string $month = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employee-prime:read', 'employee-prime:write'])]
    private ?bool $isPaid = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setPaid(?bool $isPaid): static
    {
        $this->isPaid = $isPaid;

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
}
