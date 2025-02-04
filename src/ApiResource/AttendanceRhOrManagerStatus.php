<?php

namespace App\ApiResource;

enum AttendanceRhOrManagerStatus: string
{
    case PRESENT = "PRESENT";
    case REPOS = "REPOS";
    case RETARD = "RETARD";
    case ABSENT = "ABSENT";
    case MALADE = "MALADE";
    case CONGE_CIRC = "CONGE CIRC";
    case CONGE_CIRC_NP = "CONGE CIRC NP";
    case SUSPENSION = "SUSPENSION";
}