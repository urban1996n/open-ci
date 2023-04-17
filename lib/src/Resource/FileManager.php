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
        foreach (\scandir($directoryPath) as $file) {
            if (!\in_array($file, ['..', '.'])) {
                if (\is_dir($directoryPath . '/' . $file) && !is_link($directoryPath . "/" . $file)) {
                    $this->removeDir($directoryPath . '/' . $file);
                } else {
                    \unlink($directoryPath . '/' . $file);
                }
            }
        }

        \rmdir($directoryPath);
    }
}
