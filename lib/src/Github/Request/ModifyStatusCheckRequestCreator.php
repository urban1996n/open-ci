<?php

namespace App\Github\Request;

use App\Job\Job;

class ModifyStatusCheckRequestCreator extends AbstractStatusRequestCreator
{
    protected function getMethod(?object $subject): string
    {
        return 'POST';
    }

    protected function getRequestBody(?object $subject): array
    {
        /** @var Job $subject */
        return parent::getRequestBody($subject) + ['state' => $subject->getStatus()->name];
    }
}
