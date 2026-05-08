<?php 
namespace App\Enum;

enum TaskPriority: string {
    case Minor = 'Minor';
    case Moderate = 'Moderate';
    case Major = 'Major';
}