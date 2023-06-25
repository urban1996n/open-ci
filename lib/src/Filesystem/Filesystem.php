<?php

namespace App\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as Fs;

class Filesystem extends Fs
{
    public function getContents(string $fileName): string
    {
        if (!$this->exists($fileName)) {
            throw new IOException('The file you want to get a content of does not exist');
        }

        return \file_get_contents($fileName);
    }
}
