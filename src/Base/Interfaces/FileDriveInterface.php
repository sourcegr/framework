<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Interfaces;


    interface FileDriveInterface
    {
        public function getRealPath(string $directory = null): string;

        public function getDriveName(): string;

        public function getDrivePath(): string;

        public function isWritable(string $directory = ''): bool;

        public function isReadable(string $directory = ''): bool;

        public function saveFile(string $filenameWithPath, string $toFilename, string $onDirectory = ''): bool;

        public function deleteFile(string $filenameWithPath): bool;

        public function createDirectory(string $directory, bool $mode = false, bool $recursive = false): bool;

        public function getFileList(string $directory = ''): array;

        public function getDirectoryList(string $directory = ''): array;
    }