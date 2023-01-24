<?php

namespace App\Pipeline\Exception;

class InvalidSchemaException extends PipelineException
{
    public function __construct(array $schemaErrors)
    {
        parent::__construct(
            'Invalid json schema provided for pipeline. Validation result: ' . \implode(', ', $schemaErrors)
        );
    }
}