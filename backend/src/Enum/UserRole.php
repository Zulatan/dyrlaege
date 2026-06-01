<?php 
namespace App\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case VET = 'ROLE_VET';
    case RECEPTIONIST = 'ROLE_RECEPTIONIST';
}