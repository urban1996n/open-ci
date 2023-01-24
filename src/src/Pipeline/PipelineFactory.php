<?php

namespace App\Pipeline;

use App\Pipeline\Exception\PipelineException;

class PipelineFactory
{
    private Assembler $pipelineAssembler;

    private SchemaValidator $validator;

    private string $pipelinePath;

    public function __construct(
        SchemaValidator $validator,
        Assembler $pipelineAssembler,
        string $pipelinePath,
        string $rootDir
    ) {
        $this->pipelineAssembler = $pipelineAssembler;
        $this->validator         = $validator;
        $this->pipelinePath      = $rootDir . '/' . $pipelinePath;
    }

    /** @throws PipelineException */
    public function create(): Pipeline
    {
        $this->validator->validate($this->pipelinePath);

        return $this->pipelineAssembler->assemble(\json_decode(\file_get_contents($this->pipelinePath), true));
    }
}