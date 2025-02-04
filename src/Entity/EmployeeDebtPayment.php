<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EmployeeDebtPaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['employeeDebtPayment:read']],
    denormalizationContext: ['groups' => ['employeeDebtPayment:write']],
)]
#[ORM\Entity(repositoryClass: EmployeeDebtPaymentRepository::class)]
class EmployeeDebtPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['employeeDebtPayment:read',])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employeeDebtPayments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employeeDebtPayment:read','employeeDebtPayment:write'])]
    private ?EmployeeDebt $employeeDebt = null;

    #[ORM\Column]
    #[Groups(['employeeDebtPayment:read','employeeDebtPayment:write'])]
    private ?float $amount = null;

    #[ORM\Column]
    #[Groups(['employeeDebtPayment:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'employeeDebtPayments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employeeDebtPayment:read','employeeDebtPayment:write'])]
    private ?EmployeePayment $employeePayment = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeDebt(): ?EmployeeDebt
    {
        return $this->employeeDebt;
    }

    public function setEmployeeDebt(?EmployeeDebt $employeeDebt): static
    {
        $this->employeeDebt = $employeeDebt;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEmployeePayment(): ?EmployeePayment
    {
        return $this->employeePayment;
    }

    public function setEmployeePayment(?EmployeePayment $employeePayment): static
    {
        $this->employeePayment = $employeePayment;

        return $this;
    }
}
