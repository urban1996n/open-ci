<?php

namespace App\Pipeline;

use App\Job\Job;
use App\Pipeline\Data\Pipeline;
use App\Pipeline\Exception\AssembleException;
use App\Pipeline\Exception\PipelineException;
use App\Resource\Locator;

class PipelineFactory
{
    public function __construct(
        private readonly SchemaValidator $validator,
        private readonly Assembler $pipelineAssembler,
    ) {
    }

    /** @throws PipelineException */
    public function create(string $pipelinePath): Pipeline
    {
        $this->validator->validate($pipelinePath);

        try {
            return $this->pipelineAssembler->assemble(\json_decode(\file_get_contents($pipelinePath), true));
        } catch (\Throwable $throwable) {
            throw new AssembleException($throwable->getMessage());
        }
    }
}
