<?php

namespace App\Pipeline\Exception;

class MissingEnvVariableException extends PipelineException
{
    public function __construct(string | array $envVarName)
    {
        $envVarName = \is_string($envVarName) ? $envVarName : \implode(',', $envVarName);
        parent::__construct('Expected ' . $envVarName . ' env variable(s) to be defined, but it\'s undefined');
    }
}
