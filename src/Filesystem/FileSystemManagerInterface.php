<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem;


    use Sourcegr\Framework\Filesystem\Engine\FileSystemDriveInterface;

    interface FileSystemManagerInterface
    {
        public function createDrive(string $driveName, array $drive): FileSystemManagerInterface;

        public function attachDrive(string $name, FileSystemDriveInterface $drive): FileSystemManagerInterface;

        public function drive(string $driveName): ?FileSystemDriveInterface;

        public function getDrives(): array;
    }