<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AttendanceStatus;
use App\Entity\Attendance;
use Doctrine\ORM\EntityManagerInterface;

class AttendanceStateProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if(!$data instanceof Attendance){
            return;
        }

        $repository = $this->entityManager->getRepository(Attendance::class);
        $existEntity = $repository->findOneBy(['employee' => $data->getEmployee(), 'date_id' => $data->getDateId()]);

        if ($existEntity && $existEntity->getStatus() === AttendanceStatus::ABSENT) {
            $existEntity->setStatus($data->getStatus());

            $data = $existEntity;
        }

//        $this->innerpro
    }
}
