<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class JsonToInputBagDecorator
{
    private JsonDecode $decoder;

    public function __construct()
    {
        $this->decoder = new JsonDecode([JsonDecode::OPTIONS => \JSON_THROW_ON_ERROR, JsonDecode::ASSOCIATIVE => true]);
    }

    /** @throws NotEncodableValueException */
    public function decorate(string $json): InputBag
    {
        $inputBag    = new InputBag();
        $decodedJson = $this->decoder->decode($json, JsonEncoder::FORMAT);

        foreach ($decodedJson as $key => $value) {
            $inputBag->set($key, $value);
        }

        return $inputBag;
    }
}
