<?php

namespace App\Pipeline\Data\Component;

class EnvVar
{
    private string $name;

    private mixed $value;

    public function __construct(string $name, mixed $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public static function fromArray(array $initializer): EnvVar
    {
        $name  = $initializer['name'];
        $value = $initializer['value'];

        return new self($name, $value);
    }
}