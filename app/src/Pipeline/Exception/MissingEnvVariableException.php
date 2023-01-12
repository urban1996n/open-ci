<?php

namespace App\Pipeline\Exception;

class MissingEnvVariableException extends PipelineException
{
    public function __construct(string $envVarName)
    {
        parent::__construct('Expected ' . $envVarName . ' env variable to be defined, but it\'s undefined');
    }
}