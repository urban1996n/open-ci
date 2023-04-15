<?php

namespace App\Pipeline\Exception;

class MissingSchemaFileException extends PipelineException
{
    public function __construct(string $filePath)
    {
        parent::__construct('Expected to find schema file under ' . $filePath . ' but no such file exists');
    }
}
