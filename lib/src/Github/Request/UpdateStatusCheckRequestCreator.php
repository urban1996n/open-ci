<?php

namespace App\Github\Request;

use App\Common\Status;
use App\Job\Data\Config;
use App\Job\Job;

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
        /** @var Config $subject */
        return parent::getRequestBody($subject) + ['state' => $context['status'] ?? Status::Failure->value];
    }
}
