<?php

namespace App\Pipeline\Data\Component;

class Argument
{
    private string $name;

    private mixed $value;

    public function __construct(string $name, mixed $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->name . ' ' . $this->value;
    }

    public static function fromArray($initializer): Argument
    {
        $name  = $initializer['name'];
        $value = $initializer['value'];

        return new self($name, $value);
    }
}