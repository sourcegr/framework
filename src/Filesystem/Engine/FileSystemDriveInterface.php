<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem\Engine;


    interface FileSystemDriveInterface
    {
        public function getDrivePath(): string;

        public function isWritable(string $directory = ''): bool;

        public function isReadable(string $directory = ''): bool;

        public function saveFile(string $filenameWithPath, string $toFilename, string $onDirectory = '');

        public function saveFileUniq(string $filenameWithPath, string $toFilename, string $onDirectory = '');

        public function deleteFile(string $filenameWithPath, string $onDirectory = ''): bool;

        public function createDirectory(string $directory, bool $recursive = false, int $mode = 0755): bool;

        public function deleteDirectory(string $directory, bool $recursive = false): bool;

        public function getFileList(string $directory = ''): array;

        public function getDirectoryList(string $directory = ''): array;

        public function fileExists(string $file, string $directory = ''): bool;
    }