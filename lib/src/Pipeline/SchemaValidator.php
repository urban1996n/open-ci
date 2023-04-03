<?php

namespace App\Pipeline;

use App\Pipeline\Exception\InvalidSchemaException;
use App\Pipeline\Exception\MissingSchemaFileException;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;

class SchemaValidator
{
    private Validator $validator;

    private string $pipelineSchemaUrl;

    public function __construct(string $pipelineSchemaUrl)
    {
        $this->validator         = new Validator();
        $this->pipelineSchemaUrl = $pipelineSchemaUrl;
    }

    public function validate(string $pipelinePath): bool
    {
        if (!\file_exists($pipelinePath)) {
            throw new MissingSchemaFileException($pipelinePath);
        }

        $validationResult = $this->validator->validate(
            \json_decode(\file_get_contents($pipelinePath)),
            \file_get_contents($this->pipelineSchemaUrl)
        );

        if ($validationResult->isValid()) {
            return true;
        }

        throw new InvalidSchemaException(\array_map($this->serializeErrors(), $validationResult->error()->subErrors()));
    }

    private function serializeErrors(): \Closure
    {
        return function (ValidationError $error): string {
            return $error->message();
        };
    }
}
