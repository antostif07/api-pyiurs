<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;    

class AttendanceAssignmentNameFilter extends AbstractFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if ($property !== 'assignmentName') {
            return;
        }

        // Ajoutez votre logique de filtrage ici
        $queryBuilder
            ->join('o.employee', 'e')
            ->join('e.assignment', 'a')
            ->andWhere('a.name LIKE :value')
            ->setParameter('value', '%'.$value.'%');
    }
    public function getDescription(string $resourceClass): array
    {
        return [
            'assignmentName' => [
                'property' => 'assignment',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by Assignment name',
                ],
            ],
        ];
    }
}