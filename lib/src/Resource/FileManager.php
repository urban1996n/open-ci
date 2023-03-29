<?php

namespace App\Resource;

class FileManager
{
    public function createDirectory(string $destinationPath): void
    {
        \mkdir($destinationPath, 0777, true);
    }

    public function removeDir(string $directoryPath): void
    {
        $directory = \scandir($directoryPath);
        if (!$directory) {
            return;
        }

        $files = \array_diff($directory, ['.', '..']);

        foreach ($files as $file) {
            $current = $directoryPath . '/' . $file;
            \is_dir($current)
                ? $this->removeDir($current)
                : \unlink($current);
        }

        \rmdir($directoryPath);
    }
}
