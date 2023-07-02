<?php

namespace App\Github\Request;

use App\Job\Config;

class MarkCheckFailureRequestCreator extends AbstractStatusRequestCreator
{
    public function supports(RequestType $type, ?object $subject): bool
    {
        return parent::supports($type, $subject) && $type === RequestType::COMMIT_STATUS_INIT;
    }

    protected function getMethod(?object $subject): string
    {
        return 'POST';
    }

    protected function getRequestBody(?object $subject, array $context = []): array
    {
        if (!$subject instanceof Config) {
            throw RequestCreationException::invalidSubject(
                Config::class, $subject === null ? 'null' : \get_class($subject)
            );
        }

        return parent::getRequestBody($subject) + ['state' => 'failure', 'details_url' => $this->getDetailsUrlFor($subject)];
    }
}
