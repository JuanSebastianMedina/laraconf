<?php

namespace App\Enums;

enum TalkStatus: string
{
    case SUBMITTED = 'Submitted';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';

    public function getColor(): string
    {
        return match ($this){
            self::SUBMITTED => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
