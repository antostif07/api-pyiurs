<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Filter\AssignmentNameFilter;
use App\Repository\EmployeeRepository;
use App\State\EmployeeCollectionStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(provider: EmployeeCollectionStateProvider::class,),
        new Get(),
        new Post(),
        new Patch(),
        new Delete()
     ],
    normalizationContext: ['groups' => ['employee:read']],
    denormalizationContext: ['groups' => ['employee:write']],
    security: "is_granted('ROLE_USER')",
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'ipartial', "assignment.name" => "ipartial", "job_status" => "ipartial",
    "employee_function" => "ipartial",
])]
#[UniqueEntity('name')]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['employee:read', 'attendance:read', 'employeePayment:read', 'employee-prime:read', "user:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read', 'employee-prime:read', "user:read"])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    #[Assert\NotBlank]
    #[ApiFilter(AssignmentNameFilter::class)]
    private ?Assignment $assignment = null;

    #[ORM\Column]
    #[Groups(['employee:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?int $transportFee = null;

    #[ORM\Column]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?int $salary = null;

    /**
     * @var Collection<int, Attendance>
     */
    #[ORM\OneToMany(targetEntity: Attendance::class, mappedBy: 'employee')]
    private Collection $attendances;

    #[ORM\Column(nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?int $total_days = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?string $employee_function = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?string $matricule = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?string $job_status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?string $team = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?string $department = null;

    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    /**
     * @var Collection<int, EmployeePrime>
     */
    #[ORM\OneToMany(targetEntity: EmployeePrime::class, mappedBy: 'employee')]
    private Collection $employeePrimes;

    /**
     * @var Collection<int, EmployeeDebt>
     */
    #[ORM\OneToMany(targetEntity: EmployeeDebt::class, mappedBy: 'employee')]
    private Collection $employeeDebts;

    #[ORM\Column(nullable: true)]
    #[Groups(['employee:read', 'employee:write', 'attendance:read', 'employeePayment:read'])]
    private ?float $indemnityKm = null;

    /**
     * @var Collection<int, EmployeePayment>
     */
    #[ORM\OneToMany(targetEntity: EmployeePayment::class, mappedBy: 'employee')]
    private Collection $employeePayments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->attendances = new ArrayCollection();
        $this->employeePayments = new ArrayCollection();
        $this->employeePrimes = new ArrayCollection();
        $this->employeeDebts = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTransportFee(): ?int
    {
        return $this->transportFee;
    }

    public function setTransportFee(int $transportFee): static
    {
        $this->transportFee = $transportFee;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(int $salary): static
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * @return Collection<int, Attendance>
     */
    public function getAttendances(): Collection
    {
        return $this->attendances;
    }

    public function addAttendance(Attendance $attendance): static
    {
        if (!$this->attendances->contains($attendance)) {
            $this->attendances->add($attendance);
            $attendance->setEmployee($this);
        }

        return $this;
    }

    public function removeAttendance(Attendance $attendance): static
    {
        if ($this->attendances->removeElement($attendance)) {
            // set the owning side to null (unless already changed)
            if ($attendance->getEmployee() === $this) {
                $attendance->setEmployee(null);
            }
        }

        return $this;
    }

    public function getTotalDays(): ?int
    {
        return $this->total_days;
    }

    public function setTotalDays(?int $total_days): static
    {
        $this->total_days = $total_days;

        return $this;
    }

    public function getEmployeeFunction(): ?string
    {
        return $this->employee_function;
    }

    public function setEmployeeFunction(?string $employee_function): static
    {
        $this->employee_function = $employee_function;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getJobStatus(): ?string
    {
        return $this->job_status;
    }

    public function setJobStatus(?string $job_status): static
    {
        $this->job_status = $job_status;

        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * @return Collection<int, EmployeePrime>
     */
    public function getEmployeePrimes(): Collection
    {
        return $this->employeePrimes;
    }

    public function addEmployeePrime(EmployeePrime $employeePrime): static
    {
        if (!$this->employeePrimes->contains($employeePrime)) {
            $this->employeePrimes->add($employeePrime);
            $employeePrime->setEmployee($this);
        }

        return $this;
    }

    public function removeEmployeePrime(EmployeePrime $employeePrime): static
    {
        if ($this->employeePrimes->removeElement($employeePrime)) {
            // set the owning side to null (unless already changed)
            if ($employeePrime->getEmployee() === $this) {
                $employeePrime->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EmployeeDebt>
     */
    public function getEmployeeDebts(): Collection
    {
        return $this->employeeDebts;
    }

    public function addEmployeeDebt(EmployeeDebt $employeeDebt): static
    {
        if (!$this->employeeDebts->contains($employeeDebt)) {
            $this->employeeDebts->add($employeeDebt);
            $employeeDebt->setEmployee($this);
        }

        return $this;
    }

    public function removeEmployeeDebt(EmployeeDebt $employeeDebt): static
    {
        if ($this->employeeDebts->removeElement($employeeDebt)) {
            // set the owning side to null (unless already changed)
            if ($employeeDebt->getEmployee() === $this) {
                $employeeDebt->setEmployee(null);
            }
        }

        return $this;
    }

    public function getIndemnityKm(): ?float
    {
        return $this->indemnityKm;
    }

    public function setIndemnityKm(?float $indemnityKm): static
    {
        $this->indemnityKm = $indemnityKm;

        return $this;
    }

    /**
     * @return Collection<int, EmployeePayment>
     */
    public function getEmployeePayments(): Collection
    {
        return $this->employeePayments;
    }

    public function addEmployeePayment(EmployeePayment $employeePayment): static
    {
        if (!$this->employeePayments->contains($employeePayment)) {
            $this->employeePayments->add($employeePayment);
            $employeePayment->setEmployee($this);
        }

        return $this;
    }

    public function removeEmployeePayment(EmployeePayment $employeePayment): static
    {
        if ($this->employeePayments->removeElement($employeePayment)) {
            // set the owning side to null (unless already changed)
            if ($employeePayment->getEmployee() === $this) {
                $employeePayment->setEmployee(null);
            }
        }

        return $this;
    }

    #[Groups(['employee:read'])]
    public function getEmployeeDebtAmount(): float
    {
        $totalDebt = 0;

        foreach ($this->getEmployeeDebts() as $debt) {
            $totalDebt += $debt->getRest();
        }
        
        return $totalDebt;
    }
}
