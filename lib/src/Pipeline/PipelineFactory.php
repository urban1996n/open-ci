<?php

namespace App\Pipeline;

use App\Pipeline\Data\Pipeline;
use App\Pipeline\Exception\PipelineException;

class PipelineFactory
{
    private Assembler $pipelineAssembler;

    private SchemaValidator $validator;

    public function __construct(SchemaValidator $validator, Assembler $pipelineAssembler)
    {
        $this->pipelineAssembler = $pipelineAssembler;
        $this->validator         = $validator;
    }

    /** @throws PipelineException */
    public function create(string $pipelinePath): Pipeline
    {
        $this->validator->validate($pipelinePath);

        return $this->pipelineAssembler->assemble(\json_decode(\file_get_contents($pipelinePath), true));
    }
}
