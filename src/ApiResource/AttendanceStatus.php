<?php

namespace App\ApiResource;

enum AttendanceStatus: string
{
    case PRESENT = "PRESENT";
    case REPOS = "REPOS";
    case R1 = "R -1";
    case R2 = "R -2";
    case RETARD = "RETARD";
    case ABSENT = "ABSENT";
    case MALADE = "MALADE";
    case CONGE_CIRC = "CONGE CIRC";
    case CONGE_CIRC_NP = "CONGE CIRC NP";
    case SUSPENSION = "SUSPENSION";
}