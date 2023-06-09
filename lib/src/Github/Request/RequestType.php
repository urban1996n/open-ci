<?php

namespace App\Github\Request;

enum RequestType: int
{
    case COMMIT_STATUS_INIT = 0;
    case COMMIT_STATUS_UPDATE = 1;
    case COMMIT_STATUS_FAILURE = 2;
    case REPOSITORY_DOWNLOAD = 3;
}
