<?php

namespace App\Github\Request;

use App\Common\Status;
use App\Job\Data\Config;

class UpdateStatusCheckRequestCreator extends AbstractStatusRequestCreator
{
    public function supports(RequestType $type, ?object $subject): bool
    {
        return parent::supports($type, $subject) && $type === RequestType::COMMIT_STATUS_UPDATE;
    }

    protected function getMethod(?object $subject): string
    {
        return 'POST';
    }

    protected function getRequestBody(?object $subject, array $context = []): array
    {
        if (!$subject instanceof Config) {
            throw new \RuntimeException();
        }

        return parent::getRequestBody($subject) +
            [
                'state'       => $context['status'] ?? Status::Failure->value,
                'details_url' => $this->getDetailsUrlFor($subject),
            ];
    }
}
