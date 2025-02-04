<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;    

class EmployeeNameFilter extends AbstractFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if ($property !== 'employeeName') {
            return;
        }

        $queryBuilder
            ->join('o.employee', 'em')
            ->andWhere('emp.name LIKE :value')
            ->setParameter('value', '%'.$value.'%');
    }
    public function getDescription(string $resourceClass): array
    {
        return [
            'employeeName' => [
                'property' => 'employee',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by Employee name',
                ],
            ],
        ];
    }
}