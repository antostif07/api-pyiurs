<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiResource\AttendanceRhOrManagerStatus;
use App\ApiResource\AttendanceStatus;
use App\Filter\AttendanceFilter;
use App\Filter\EmployeeNameFilter;
use App\Repository\AttendanceRepository;
use App\State\AttendanceStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;

#[ORM\Entity(repositoryClass: AttendanceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Put()

    ],
    normalizationContext: ['groups' => ['attendance:read']],
    denormalizationContext: ['groups' => ['attendance:write']],
    order: ["employee.name" => "ASC", 'attendanceDateTime' => 'ASC',]
)]
#[ApiFilter(AttendanceFilter::class)]
#[ApiFilter(SearchFilter::class, properties: [
    'date_id' => 'exact', "attendanceDateTime" => "ipartial", 'employee.id' => 'exact', "employee" => "exact",
    "status" => "ipartial", "managerStatus" => "ipartial", "rhStatus" => "ipartial",
    ])]
#[ApiFilter(DateFilter::class, properties: ['attendanceDateTime'])]
#[UniqueEntity(
    fields: ['employee', 'date_id']
)]
class Attendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['attendance:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    private ?\DateTimeImmutable $attendanceDateTime = null;

    #[ORM\Column(length: 255)]
    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    private ?string $date_id = null;

    #[ORM\ManyToOne(inversedBy: 'attendances')]
    #[ApiFilter(EmployeeNameFilter::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    private ?Employee $employee = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    private ?string $observation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    private ?bool $isValid = null;

    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    #[ORM\Column(length: 255)]
    private ?AttendanceStatus $status = null;

    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?AttendanceRhOrManagerStatus $managerStatus = null;

    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?AttendanceRhOrManagerStatus $rhStatus = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: MediaObject::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[ApiProperty(types: ['https://schema.org/media'])]
    #[Groups(['employee:read', 'attendance:write', 'attendance:read'])]
    public ?MediaObject $mediaFile = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttendanceDateTime(): ?\DateTimeImmutable
    {
        return $this->attendanceDateTime;
    }

    public function setAttendanceDateTime(\DateTimeImmutable $attendanceDateTime): static
    {
        $this->attendanceDateTime = $attendanceDateTime;

        return $this;
    }

    public function getDateId(): ?string
    {
        return $this->date_id;
    }

    public function setDateId(string $date_id): static
    {
        $this->date_id = $date_id;

        return $this;
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

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->isValid;
    }

    public function setValid(?bool $isValid): static
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getStatus(): ?AttendanceStatus
    {
        return $this->status;
    }

    public function setStatus(AttendanceStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getManagerStatus(): ?AttendanceRhOrManagerStatus
    {
        return $this->managerStatus;
    }

    public function setManagerStatus(?AttendanceRhOrManagerStatus $managerStatus): static
    {
        $this->managerStatus = $managerStatus;

        return $this;
    }

    public function getRhStatus(): ?AttendanceRhOrManagerStatus
    {
        return $this->rhStatus;
    }

    public function setRhStatus(?AttendanceRhOrManagerStatus $rhStatus): static
    {
        $this->rhStatus = $rhStatus;

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

    #[Groups(['attendance:read'])]
    public function getAssignmentName(): string
    {
        return $this->getEmployee()->getAssignment()->getName();
    }

    #[Groups(['attendance:read'])]
    public function getEmployeeName(): string
    {
        return $this->getEmployee()->getName();
    }
}
