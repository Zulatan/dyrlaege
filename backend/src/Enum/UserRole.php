<?php 
namespace App\Enum;

enum UserRole: string
{
    case USER = 'user';
    case SUPERUSER = 'superuser';
    case ADMIN = 'admin';
}