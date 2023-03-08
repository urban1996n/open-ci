<?php

namespace App\Github\Request;

class InitStatusCheckRequestCreator extends AbstractStatusRequestCreator
{
    protected function getMethod(?object $subject): string
    {
        return 'POST';
    }

    protected function getRequestBody(?object $subject): array
    {
        return parent::getRequestBody($subject) + ['state' => 'pending'];
    }
}
