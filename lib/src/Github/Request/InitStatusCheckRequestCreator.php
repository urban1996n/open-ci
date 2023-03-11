<?php

namespace App\Github\Request;

class InitStatusCheckRequestCreator extends AbstractStatusRequestCreator
{
    public function supports(RequestType $type, ?object $subject): bool
    {
        return parent::supports($type, $subject) && $type === RequestType::COMMIT_STATUS_INIT;
    }

    protected function getMethod(?object $subject): string
    {
        return 'POST';
    }

    protected function getRequestBody(?object $subject): array
    {
        return parent::getRequestBody($subject) + ['state' => 'pending'];
    }
}
