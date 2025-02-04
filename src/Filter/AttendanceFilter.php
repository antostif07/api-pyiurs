<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;    

class AttendanceFilter extends AbstractFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if ($property === 'employeeName') {
            $queryBuilder
            ->join('o.employee', 'emp')
            ->andWhere('emp.name LIKE :value')
            ->setParameter('value', '%'.$value.'%');   
        }

        if ($property === 'assignmentName') {
            $queryBuilder
                ->join('o.employee', 'emmm')
                ->join('emmm.assignment', 'aaa')
                ->andWhere('aaa.name LIKE :assignmentName')
                ->setParameter('assignmentName', '%'.$value.'%');
        }
    }
    public function getDescription(string $resourceClass): array
    {
        return [
            'employeeName' => [
                'property' => 'employeeName',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by Employee name',
                ],
            ],
            'assignmentName' => [
                'property' => 'assignmentName',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by Employee name',
                ],
            ],
        ];
        // return [
        //     'employeeName' => [
        //         'property' => 'employee',
        //         'type' => 'string',
        //         'required' => false,
        //         'swagger' => [
        //             'description' => 'Filter by Employee name',
        //         ],
        //     ],
        // ];
    }
}