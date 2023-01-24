<?php

namespace App\Http;

use App\Common\Connection;

class TriggerMessenger
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}
