<?php

namespace App\Common;

enum Status: string
{
    case Failure = 'failure';
    case Success = 'success';
    case Pending = 'pending';
    case InProgress = 'in_progress';

    public function isFinished(): bool
    {
        return !\in_array($this->value, [self::InProgress, self::Pending]);
    }
}
