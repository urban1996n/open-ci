<?php

namespace App\AMQP;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class JobMessage extends AMQPMessage
{
    public function __construct(string $branchName, string $commitHash, int $buildNumber = 1)
    {
        parent::__construct('');

        $this->set(
            'application_headers',
            new AMQPTable(
                [
                    'github_data' => [
                        'branch_name' => $branchName,
                        'commit_hash' => $commitHash
                    ],
                ]
            )
        );
    }
}
