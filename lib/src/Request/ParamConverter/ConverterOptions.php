<?php

namespace App\Request\ParamConverter;

use Symfony\Component\OptionsResolver\OptionsResolver as Options;

class ConverterOptions extends Options
{
    public function __construct()
    {
        foreach (ConverterOption::cases() as $option) {
            $this->define($option->value);
            $this->setAllowedTypes($option->value, $option->type());
            $this->setDefault($option->value, $option->default());
            $this->setRequired($option->value);
        }
    }
}
