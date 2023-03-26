<?php

namespace App\Resource;

class FileManager
{
    public function createDirectory(string $destinationPath): void
    {
        $currentDir = '';
        foreach (\explode('/', $destinationPath) as $directory) {
            if (\is_dir($currentDir = $currentDir . '/' . $directory)) {
                continue;
            }

            if (!\mkdir($currentDir)) {
                throw new \RuntimeException();
            }
        }
    }

    public function removeDir(string $directoryPath): void
    {
        if (\is_dir($directoryPath)) {
            $files = \scandir($directoryPath);
            foreach ($files as $file) {
                if (!$this->isDirectoryRoot($file)) {
                    $this->removeDir($directoryPath . '/' . $file);
                }
            }
            \rmdir($directoryPath);
        } else {
            \unlink($directoryPath);
        }
    }

    private function isDirectoryRoot(string $fileName): bool
    {
        return \in_array($fileName, ['.', '..']);
    }
}
