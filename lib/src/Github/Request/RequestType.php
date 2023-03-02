<?php

namespace App\Github\Request;

enum RequestType: int
{
    case COMMIT_STATUS_GET = 0;
    case COMMIT_STATUS_UPDATE = 1;
}
