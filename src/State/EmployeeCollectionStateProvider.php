<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EmployeeCollectionStateProvider implements ProviderInterface
{
    private Security $security;
    private ProviderInterface $collectionProvider;

    public function __construct(
        CollectionProvider $collectionProvider,
        Security $security,
        private readonly EmployeeRepository $employeeRepository,
        private readonly Pagination $pagination
    ) {
        $this->security = $security;
        $this->collectionProvider = $collectionProvider;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        $employees = $this->employeeRepository->findAll();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if($operation instanceof CollectionOperationInterface) {
                return $this->collectionProvider->provide($operation, $uriVariables, $context);
            }
        }

        if ($this->security->isGranted('ROLE_MANAGER')) {
            if ($user instanceof User) {
                $em = array_filter($employees, function($employee) use ($user) {
                    return $user->getCompanies()->contains($employee->getAssignment());
                });

                return new TraversablePaginator(
                    new \ArrayIterator($em),
                    $this->pagination->getPage($context),
                    $this->pagination->getLimit($operation, $context),
                    count($em)
                );
            }
        }
        return [];
    }

    private function countTotalEmployees(): int
    {
        $employees = $this->employeeRepository->findAll();
        return count($employees);
    }
}
