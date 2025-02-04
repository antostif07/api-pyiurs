<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\EmployeeDebtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    normalizationContext: ['groups' => ['employeeDebt:read']],
    denormalizationContext: ['groups' => ['employeeDebt:write']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'employee' => 'exact',
])]
#[ORM\Entity(repositoryClass: EmployeeDebtRepository::class)]
class EmployeeDebt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['employeeDebt:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employeeDebts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employeeDebt:read', 'employeeDebt:write'])]
    private ?Employee $employee = null;

    #[ORM\Column(length: 255)]
    #[Groups(['employeeDebt:read', 'employeeDebt:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['employeeDebt:read', 'employeeDebt:write'])]
    private ?float $amount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeeDebt:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, EmployeeDebtPayment>
     */
    #[ORM\OneToMany(targetEntity: EmployeeDebtPayment::class, mappedBy: 'employeeDebt')]
    private Collection $employeeDebtPayments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->employeeDebtPayments = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, EmployeeDebtPayment>
     */
    public function getEmployeeDebtPayments(): Collection
    {
        return $this->employeeDebtPayments;
    }

    public function addEmployeeDebtPayment(EmployeeDebtPayment $employeeDebtPayment): static
    {
        if (!$this->employeeDebtPayments->contains($employeeDebtPayment)) {
            $this->employeeDebtPayments->add($employeeDebtPayment);
            $employeeDebtPayment->setEmployeeDebt($this);
        }

        return $this;
    }

    public function removeEmployeeDebtPayment(EmployeeDebtPayment $employeeDebtPayment): static
    {
        if ($this->employeeDebtPayments->removeElement($employeeDebtPayment)) {
            // set the owning side to null (unless already changed)
            if ($employeeDebtPayment->getEmployeeDebt() === $this) {
                $employeeDebtPayment->setEmployeeDebt(null);
            }
        }

        return $this;
    }

    
    #[Groups(['employeeDebt:read'])]
    public function getIsTotallyPaid(): bool
    {
        $totalPaid = 0;

        foreach ($this->getEmployeeDebtPayments() as $key) {
            $totalPaid += $key->getAmount();
        }

        return $this->getAmount() == $totalPaid;
    }

    
    #[Groups(['employeeDebt:read'])]
    public function getRest(): float
    {
        $totalPaid = 0;

        foreach ($this->getEmployeeDebtPayments() as $key) {
            $totalPaid += $key->getAmount();
        }

        return $this->getAmount() - $totalPaid;
    }
}
