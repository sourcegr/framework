<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Interfaces\Filesystem;


    interface FileSystemManagerInterface
    {
        public function attachDrive(string $driveName, FileSystemDriveInterface $drive): FileSystemManagerInterface;

        public function drive(string $driveName): ?FileSystemDriveInterface;

        public function getDrives(): array;
    }