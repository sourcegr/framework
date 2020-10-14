<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem;



    use Sourcegr\Framework\Interfaces\Filesystem\FileSystemDriveInterface;
    use Sourcegr\Framework\Interfaces\Filesystem\FileSystemManagerInterface;

    class FileSystemManager implements FileSystemManagerInterface
    {
        protected $drives = [];

        public function attachDrive(string $driveName, FileSystemDriveInterface $drive): FileSystemManagerInterface
        {
            $this->drives[$driveName] = $drive;
            return $this;
        }

        public function drive(string $driveName) : ?FileSystemDriveInterface
        {
            $drive = $this->drives[$driveName] ?? null;

            if ($drive === null) {
                throw new \Exception('Drive does not exist');
            }
            return $drive;
        }

//        public function createDrive(string $driveName, array $driveConfig): void
//        {
//            $drive = $this->useDrive($driveName, $driveConfig);
//            $this->attachDrive($driveName, $drive);
//        }
//
//        protected function useDrive(string $driveName, array $driveConfig): FileSystemDriveInterface
//        {
//            $driveEngineClass = $driveConfig['engine'];
//
//            if (!class_exists($driveEngineClass)) {
//                throw new \Exception('engine does not exist'. $driveEngineClass);
//            }
//
//            $drive = new $driveEngineClass($driveName, $driveConfig['path']);
//            return $drive;
//        }
        /**
         * @return array
         */
        public function getDrives(): array
        {
            return $this->drives;
        }
    }