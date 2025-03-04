<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\EmployeePaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    normalizationContext: ['groups' => ['employeePayment:read']],
    denormalizationContext: ['groups' => ['employeePayment:write']]
)]

#[ApiFilter(SearchFilter::class, properties: [
    'month' => 'ipartial', 'employee.id' => 'exact'
])]
#[ORM\Entity(repositoryClass: EmployeePaymentRepository::class)]
class EmployeePayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['employeePayment:read',])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employeePayments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?Employee $employee = null;

    #[ORM\Column(length: 7)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?string $month = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retR1 = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retRetR1 = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retR2 = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retRetR2 = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $absence = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retAbsence = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $totalRet = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $totalPay = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $nap = null;

    #[ORM\Column]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, EmployeeDebtPayment>
     */
    #[ORM\OneToMany(targetEntity: EmployeeDebtPayment::class, mappedBy: 'employeePayment')]
    private Collection $employeeDebtPayments;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $transportAbs = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $malade = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retMalade = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $transportMalade = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $cCirc = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retCCirc = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $transportCCirc = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $cCircNP = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retCCircNP = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $transportCCircNP = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $suspension = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $retSuspension = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $transportSuspension = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $remMalade = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    private ?float $remCC = null;
    
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

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function getRetR1(): ?float
    {
        return $this->retR1;
    }

    public function setRetR1(?float $retR1): static
    {
        $this->retR1 = $retR1;

        return $this;
    }

    public function getRetRetR1(): ?float
    {
        return $this->retRetR1;
    }

    public function setRetRetR1(?float $retRetR1): static
    {
        $this->retRetR1 = $retRetR1;

        return $this;
    }

    public function getRetR2(): ?float
    {
        return $this->retR2;
    }

    public function setRetR2(?float $retR2): static
    {
        $this->retR2 = $retR2;

        return $this;
    }

    public function getRetRetR2(): ?float
    {
        return $this->retRetR2;
    }

    public function setRetRetR2(?float $retRetR2): static
    {
        $this->retRetR2 = $retRetR2;

        return $this;
    }

    public function getAbsence(): ?float
    {
        return $this->absence;
    }

    public function setAbsence(?float $absence): static
    {
        $this->absence = $absence;

        return $this;
    }

    public function getRetAbsence(): ?float
    {
        return $this->retAbsence;
    }

    public function setRetAbsence(?float $retAbsence): static
    {
        $this->retAbsence = $retAbsence;

        return $this;
    }

    public function getTotalRet(): ?float
    {
        return $this->totalRet;
    }

    public function setTotalRet(?float $totalRet): static
    {
        $this->totalRet = $totalRet;

        return $this;
    }

    public function getTotalPay(): ?float
    {
        return $this->totalPay;
    }

    public function setTotalPay(?float $totalPay): static
    {
        $this->totalPay = $totalPay;

        return $this;
    }

    public function getNap(): ?float
    {
        return $this->nap;
    }

    public function setNap(?float $nap): static
    {
        $this->nap = $nap;

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
            $employeeDebtPayment->setEmployeePayment($this);
        }

        return $this;
    }

    public function removeEmployeeDebtPayment(EmployeeDebtPayment $employeeDebtPayment): static
    {
        if ($this->employeeDebtPayments->removeElement($employeeDebtPayment)) {
            // set the owning side to null (unless already changed)
            if ($employeeDebtPayment->getEmployeePayment() === $this) {
                $employeeDebtPayment->setEmployeePayment(null);
            }
        }

        return $this;
    }

    public function getTransportAbs(): ?float
    {
        return $this->transportAbs;
    }

    public function setTransportAbs(?float $transportAbs): static
    {
        $this->transportAbs = $transportAbs;

        return $this;
    }

    public function getMalade(): ?float
    {
        return $this->malade;
    }

    public function setMalade(?float $malade): static
    {
        $this->malade = $malade;

        return $this;
    }

    public function getRetMalade(): ?float
    {
        return $this->retMalade;
    }

    public function setRetMalade(?float $retMalade): static
    {
        $this->retMalade = $retMalade;

        return $this;
    }

    public function getTransportMalade(): ?float
    {
        return $this->transportMalade;
    }

    public function setTransportMalade(?float $transportMalade): static
    {
        $this->transportMalade = $transportMalade;

        return $this;
    }

    public function getCCirc(): ?float
    {
        return $this->cCirc;
    }

    public function setCCirc(?float $cCirc): static
    {
        $this->cCirc = $cCirc;

        return $this;
    }

    public function getRetCCirc(): ?float
    {
        return $this->retCCirc;
    }

    public function setRetCCirc(?float $retCCirc): static
    {
        $this->retCCirc = $retCCirc;

        return $this;
    }

    public function getTransportCCirc(): ?float
    {
        return $this->transportCCirc;
    }

    public function setTransportCCirc(?float $transportCCirc): static
    {
        $this->transportCCirc = $transportCCirc;

        return $this;
    }

    public function getCCircNP(): ?float
    {
        return $this->cCircNP;
    }

    public function setCCircNP(?float $cCircNP): static
    {
        $this->cCircNP = $cCircNP;

        return $this;
    }

    public function getRetCCircNP(): ?float
    {
        return $this->retCCircNP;
    }

    public function setRetCCircNP(?float $retCCircNP): static
    {
        $this->retCCircNP = $retCCircNP;

        return $this;
    }

    public function getTransportCCircNP(): ?float
    {
        return $this->transportCCircNP;
    }

    public function setTransportCCircNP(?float $transportCCircNP): static
    {
        $this->transportCCircNP = $transportCCircNP;

        return $this;
    }

    public function getSuspension(): ?float
    {
        return $this->suspension;
    }

    public function setSuspension(?float $suspension): static
    {
        $this->suspension = $suspension;

        return $this;
    }

    public function getRetSuspension(): ?float
    {
        return $this->retSuspension;
    }

    public function setRetSuspension(?float $retSuspension): static
    {
        $this->retSuspension = $retSuspension;

        return $this;
    }

    public function getTransportSuspension(): ?float
    {
        return $this->transportSuspension;
    }

    public function setTransportSuspension(?float $transportSuspension): static
    {
        $this->transportSuspension = $transportSuspension;

        return $this;
    }

    public function getRemMalade(): ?float
    {
        return $this->remMalade;
    }

    public function setRemMalade(?float $remMalade): static
    {
        $this->remMalade = $remMalade;

        return $this;
    }

    public function getRemCC(): ?float
    {
        return $this->remCC;
    }

    public function setRemCC(?float $remCC): static
    {
        $this->remCC = $remCC;

        return $this;
    }

    
    #[Groups(['employeePayment:read',])]
    public function getTotalPaidDebts(): float
    {
        $total = 0;

        foreach ($this->getEmployeeDebtPayments() as $key) {
            $total += $key->getAmount();
        }

        return $total;
    }

    #[Groups(['employeePayment:read', 'employeePayment:write'])]
    public function getPrime(): float
    {
        $totalPrime = 0;

        $primes = $this->getEmployee()->getEmployeePrimes();

        foreach($primes as $p) {
            if($p->getMonth() == $this->getMonth()){
                $totalPrime += $p->getAmount();
            }
        }
        return $totalPrime;
    }
}
