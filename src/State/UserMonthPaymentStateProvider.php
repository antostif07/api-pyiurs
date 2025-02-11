<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AttendanceRhOrManagerStatus;
use App\ApiResource\AttendanceStatus;
use App\ApiResource\UserMonthPayment;
use App\Entity\Attendance;
use App\Entity\EmployeePayment;
use App\Entity\EmployeePrime;
use App\Entity\User;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class UserMonthPaymentStateProvider implements ProviderInterface
{
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(
        CollectionProvider $collectionProvider,
        private readonly EmployeeRepository $employeeRepository,
        RequestStack $requestStack,
        Security $security
    )
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $employees = $this->employeeRepository->findAll();

        if ($this->security->isGranted('ROLE_MANAGER')){
            if ($user instanceof User) {

                $employees = array_filter($employees, function($employee) use ($user) {
                    return $user->getCompanies()->contains($employee->getAssignment());
                });
            }
        } else {
            $assignment = $request->query->get('assignment');

            if ($assignment) {
                $employees = array_filter($this->employeeRepository->findAll(), function ($employee) use ($assignment) {
                    return $employee->getAssignment()->getId() === intval($assignment);
                });
            }
        }
        $searchValue = $request->query->get('search');

        $paymentState = [];

        foreach ($employees as $employee) {
            $retR1 = 0;
            $retRetR1 = 0;
            $retR2 = 0;
            $retRetR2 = 0;
            $absence = 0;
            $transportAbs = 0;
            $retAbsence = 0;
            $cCirc = 0;
            $transportCCirc = 0;
            $retCCirc = 0;
            $cCircNP = 0;
            $transportCCircNP = 0;
            $retCCircNP = 0;
            $malade = 0;
            $transportMalade = 0;
            $retMalade = 0;
            $suspension = 0;
            $transportSuspension = 0;
            $retSuspension = 0;
            $presents = 0;
            // Get Employee Attendances
            $attendances = $employee->getAttendances();
            // Filter Attendances with search
            $filterUserAttendances = $attendances->filter(function(Attendance $attendance) use ($searchValue){
                $attendanceMonth = $attendance->getAttendanceDateTime()->format('Y-m');

                return $attendanceMonth == $searchValue;
            });

            if($filterUserAttendances->count() < 1) {
                continue;
            }

            $yearMonth = explode('-', $searchValue);
            $monthDays = $this->countDaysInMonthWithoutSundays($yearMonth[0], $yearMonth[1]);

            $salaryByDay = $employee->getSalary() / $monthDays;
            $transportByDay = $employee->getTransportFee() / $monthDays;
            $salaryByHour = $salaryByDay / 8.5;

            $userMonthPayment = new UserMonthPayment();
            $userMonthPayment->id = $employee->getId();
            $userMonthPayment->employeeId = $employee->getId();
            $userMonthPayment->employeeName = $employee->getName();
            $userMonthPayment->employeeTransport = $employee->getTransportFee();
            $userMonthPayment->employeeAssignment = $employee->getAssignment()->getName();
            $userMonthPayment->employeeSalary = $employee->getSalary();
            $userMonthPayment->employeeDaysOfJob = $employee->getTotalDays();
            $userMonthPayment->employeeIndKm = $employee->getIndemnityKm() ?? 0;
            $userMonthPayment->debtToPaid = $employee->getEmployeeDebtAmount();
            $prime = $employee->getEmployeePrimes()->reduce(function(float $accumulator, EmployeePrime $employeePrime) use ($searchValue): float {
                if($employeePrime->getMonth() == $searchValue){
                    return $accumulator + $employeePrime->getAmount();
                }
                return $accumulator;
            }, 0);

            $payment = $employee->getEmployeePayments()->findFirst(function(int $key, EmployeePayment $userPayment) use ($searchValue){
                return $userPayment->getMonth() == $searchValue;
            });
            $totalDebtPaid = 0;
            if(!is_null($payment)){
                foreach ($payment->getEmployeeDebtPayments() as $key) {
                    $totalDebtPaid += $key->getAmount();
                }
            }
            $userMonthPayment->debtPaid = $totalDebtPaid;
            $userMonthPayment->prime = $prime ?? 0;
            $userMonthPayment->isValid = $employee->getEmployeePayments()->filter(function(EmployeePayment $userPayment) use ($searchValue){
                    return $userPayment->getMonth() == $searchValue;
                })->count() > 0;

            // foreach Attendance
            foreach ($filterUserAttendances as $userAttendance) {
                switch ($userAttendance->getRhStatus()) {
                    case AttendanceRhOrManagerStatus::RETARD:
                        switch ($userAttendance->getStatus()) {
                            case AttendanceStatus::R1:
                                $retRetR1 += $salaryByHour;
                                $retR1 += 1;
                                $presents += 1;
                                break;

                            default:
                                $retRetR2 += $salaryByHour * 2;
                                $retR2 += 1;
                                $presents += 1;
                                break;
                        }
                        break;
                    case AttendanceRhOrManagerStatus::ABSENT:
                        $absence += 1;
                        $retAbsence += $salaryByDay;
                        $transportAbs += $transportByDay;
                        break;
                    case AttendanceRhOrManagerStatus::CONGE_CIRC:
                        $cCirc += 1;
                        $retCCirc += $salaryByDay;
                        $transportCCirc += $transportByDay;
                        break;
                    case AttendanceRhOrManagerStatus::CONGE_CIRC_NP:
                        $cCircNP += 1;
                        $retCCircNP += $salaryByDay;
                        $transportCCircNP += $transportByDay;
                        break;
                    case AttendanceRhOrManagerStatus::MALADE:
                        $malade += 1;
                        $retMalade += $salaryByDay;
                        $transportMalade += $transportByDay;
                        break;
                    case AttendanceRhOrManagerStatus::REPOS:
                    case AttendanceRhOrManagerStatus::PRESENT:
                        $presents += 1;
                        break;
                    case AttendanceRhOrManagerStatus::SUSPENSION:
                        $suspension += 1;
                        $retSuspension += $salaryByDay;
                        $transportSuspension += $transportByDay;
                        break;
                    default:
                        switch ($userAttendance->getManagerStatus()) {
                            case AttendanceRhOrManagerStatus::RETARD:
                                switch ($userAttendance->getStatus()) {
                                    case AttendanceStatus::R1:
                                        $retRetR1 += $salaryByHour;
                                        $retR1 += 1;
                                        $presents += 1;
                                        break;

                                    default:
                                        $retRetR2 += $salaryByHour * 2;
                                        $retR2 += 1;
                                        $presents += 1;
                                        break;
                                }
                                break;
                            case AttendanceRhOrManagerStatus::ABSENT:
                                $absence += 1;
                                $retAbsence += $salaryByDay;
                                $transportAbs += $transportByDay;
                                break;
                            case AttendanceRhOrManagerStatus::CONGE_CIRC:
                                $cCirc += 1;
                                $retCCirc += $salaryByDay;
                                $transportCCirc += $transportByDay;
                                break;
                            case AttendanceRhOrManagerStatus::CONGE_CIRC_NP:
                                $cCircNP += 1;
                                $retCCircNP += $salaryByDay;
                                $transportCCircNP += $transportByDay;
                                break;
                            case AttendanceRhOrManagerStatus::MALADE:
                                $malade += 1;
                                $retMalade += $salaryByDay;
                                $transportMalade += $transportByDay;
                                break;
                            case AttendanceRhOrManagerStatus::REPOS:
                            case AttendanceRhOrManagerStatus::PRESENT:
                                $presents += 1;
                                break;
                            case AttendanceRhOrManagerStatus::SUSPENSION:
                                $suspension += 1;
                                $retSuspension += $salaryByDay;
                                $transportSuspension += $transportByDay;
                                break;
                            default:
                                switch ($userAttendance->getStatus()) {
                                    case AttendanceStatus::R1:
                                        $retRetR1 += $salaryByHour;
                                        $retR1 += 1;
                                        $presents += 1;
                                        break;
                                    case AttendanceStatus::RETARD:
                                    case AttendanceStatus::R2:
                                        $retRetR2 += $salaryByHour * 2;
                                        $retR2 += 1;
                                        $presents += 1;
                                        break;
                                    case AttendanceStatus::ABSENT:
                                        $absence += 1;
                                        $retAbsence += $salaryByDay;
                                        $transportAbs += $transportByDay;
                                        break;
                                    case AttendanceStatus::CONGE_CIRC:
                                        $cCirc += 1;
                                        $retCCirc += $salaryByDay;
                                        $transportCCirc += $transportByDay;
                                        break;
                                    case AttendanceStatus::CONGE_CIRC_NP:
                                        $cCircNP += 1;
                                        $retCCircNP += $salaryByDay;
                                        $transportCCircNP += $transportByDay;
                                        break;
                                    case AttendanceStatus::MALADE:
                                        $malade += 1;
                                        $retMalade += $salaryByDay;
                                        $transportMalade += $transportByDay;
                                        break;
                                    case AttendanceStatus::REPOS:
                                    case AttendanceStatus::PRESENT:
                                        $presents += 1;
                                        break;
                                    case AttendanceStatus::SUSPENSION:
                                        $suspension += 1;
                                        $retSuspension += $salaryByDay;
                                        $transportSuspension += $transportByDay;
                                        break;
                                }
                                break;
                        }
                        break;
                }
            }

            // set values
            $userMonthPayment->retR1 = $retR1;
            $userMonthPayment->retRetR1 = $retRetR1;
            $userMonthPayment->retR2 = $retR2;
            $userMonthPayment->retRetR2 = $retRetR2;
            $userMonthPayment->absence = $absence;
            $userMonthPayment->transportAbs = $transportAbs;
            $userMonthPayment->retAbsence = $retAbsence;
            $userMonthPayment->cCirc = $cCirc;
            $userMonthPayment->transportCCirc = $transportCCirc;
            $userMonthPayment->retCCirc = $retCCirc;
            $userMonthPayment->cCircNP = $cCircNP;
            $userMonthPayment->transportCCircNP = $transportCCircNP;
            $userMonthPayment->retCCircNP = $retCCircNP;
            $userMonthPayment->malade = $malade;
            $userMonthPayment->transportMalade = $transportMalade;
            $userMonthPayment->retMalade = $retMalade;
            $userMonthPayment->suspension = $suspension;
            $userMonthPayment->transportSuspension = $transportSuspension;
            $userMonthPayment->retSuspension = $retSuspension;
            $userMonthPayment->remMalade = $malade * $salaryByDay * 0.3;
            $userMonthPayment->remCC = $cCirc * $salaryByDay * 0.7;
            $userMonthPayment->presents = $presents;

            $userMonthPayment->retTransport = $transportByDay * $userMonthPayment->presents;

            // Total Ret
            $userMonthPayment->totalRet =
                $userMonthPayment->retRetR1 + $userMonthPayment->retRetR2 +
                $userMonthPayment->retCCirc +
                $userMonthPayment->retCCircNP + $userMonthPayment->retMalade +
                $userMonthPayment->debtPaid;

            // Total Pay
            $userMonthPayment->totalPay =
                ($salaryByDay * $userMonthPayment->presents) +
                $userMonthPayment->prime +
                $userMonthPayment->remCC + $userMonthPayment->remMalade +
                $userMonthPayment->employeeIndKm - 
                $userMonthPayment->retRetR1 -
                $userMonthPayment->retRetR2 -
                $userMonthPayment->debtPaid;

            // Net A Payer
            $userMonthPayment->nap = $userMonthPayment->totalPay + $userMonthPayment->retTransport;

            // Month
            $userMonthPayment->month = $searchValue;

            $paymentState[] = $userMonthPayment;
        }

        return $paymentState;
    }

    private function countDaysInMonthWithoutSundays($year, $month): int
    {
        $count = 0;
        $date = new \DateTimeImmutable("$year-$month-01");
        while ($date->format('m') == $month) {
            // Vérifie si ce n'est pas un dimanche
            if ($date->format('N') != 7) {
                $count++;
            }
            $date = $date->modify('+1 day');
        }
        return $count;
    }
}
