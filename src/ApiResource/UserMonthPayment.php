<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\UserMonthPaymentStateProvider;

#[ApiResource(
    provider: UserMonthPaymentStateProvider::class,
)]
class UserMonthPayment
{
    public int $id;
    
    public int $employeeId;
    public string $employeeName;
    public string $employeeAssignment;
    public float $employeeSalary;
    public float $employeeTransport;
    public float $employeeDaysOfJob;
    public float $employeeIndKm;
    public string $month;
    public float $retR1;
    public float $retRetR1;
    public float $retR2;
    public float $retRetR2;
    public float $absence;
    public float $retAbsence;
    public float $transportAbs;
    public float $cCirc;
    public $retCCirc;
    public $transportCCirc;
    public float $cCircNP;
    public $retCCircNP;
    public $transportCCircNP;
    public $malade;
    public $transportMalade;
    public $retMalade;
    public $suspension;
    public $transportSuspension;
    public $retSuspension;
    public float $prime;
    public float $debtPaid;
    public float $debtToPaid;
    public float $remMalade;
    public float $remCC;
    public float $retTransport;
    public float $totalRet;
    public float $totalPay;
    public float $nap;
    public bool $isValid;
    public int $presents;

    public function __construct()
    {
    }
}