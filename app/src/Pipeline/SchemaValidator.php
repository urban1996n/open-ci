<?php

namespace App\Pipeline;

use App\Pipeline\Exception\InvalidSchemaException;
use App\Pipeline\Exception\MissingEnvVariableException;
use App\Pipeline\Exception\MissingSchemaFileException;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;

class SchemaValidator
{
    private Validator $validator;

    private string $pipelinePath;

    public function __construct()
    {
        $this->validator = new Validator();
        $pipelinePath = $_ENV[$envVar = 'ci_cd_globals_pipeline'] ?? false;
        if (false === $pipelinePath) {
            throw new MissingEnvVariableException($envVar);
        }

        $this->pipelinePath = $pipelinePath;
    }

    public function validate(): void
    {
        if (!\file_exists($this->pipelinePath)) {
            throw new MissingSchemaFileException($this->pipelinePath);
        }

        $validationResult = $this->validator->validate(
            \json_decode(\file_get_contents($this->pipelinePath)),
            \file_get_contents(
            'https://raw.githubusercontent.com/wkluz/ci-cd-system/main/docs/schemes/pipeline/schema/json-schema.json'
            )
        );

        if ($validationResult->isValid()) {
            return;
        }

        throw new InvalidSchemaException(\array_map($this->serializeErrors(),$validationResult->error()->subErrors()));
    }

    private function serializeErrors(): \Closure
    {
        return function (ValidationError $error): string {
            return $error->message();
        };
    }
}