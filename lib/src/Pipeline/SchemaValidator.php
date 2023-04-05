<?php

namespace App\Pipeline;

use App\Pipeline\Exception\InvalidSchemaException;
use App\Pipeline\Exception\MissingSchemaFileException;
use App\Resource\Locator;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;

class SchemaValidator
{
    private Validator $validator;

    public function __construct(private readonly Locator $locator)
    {
        $this->validator = new Validator();
    }

    public function validate(string $pipelinePath): bool
    {
        if (!\file_exists($pipelinePath)) {
            throw new MissingSchemaFileException($pipelinePath);
        }

        if (!\file_exists($schemaFile = $this->locator->locateConfigFile('schema/json-schema.json'))) {
            throw new MissingSchemaFileException($schemaFile);
        }

        $validationResult = $this->validator->validate(
            \json_decode(\file_get_contents($pipelinePath)),
            \file_get_contents($schemaFile)
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
