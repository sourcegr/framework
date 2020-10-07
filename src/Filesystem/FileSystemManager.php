<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem;


    use Sourcegr\Framework\Base\Interfaces\FileDriveInterface;

    class FileSystemManager
    {
        public $drives = [];

        protected function addDrive(string $driveName, FileDriveInterface $drive): void
        {
            $this->drives[$driveName] = $drive;
        }

        public function drive(string $driveName) : ?FileDriveInterface
        {
            $drive = $this->drives[$driveName] ?? null;

            if ($drive === null) {
                throw new \Exception('engine does not exist');
            }
            return $drive;
        }

        public function createDrive(string $driveName, array $driveConfig): void
        {
            $drive = $this->useDrive($driveName, $driveConfig);
            $this->addDrive($driveName, $drive);
        }

        public function useDrive(string $driveName, array $driveConfig): FileDriveInterface
        {
            $driveEngineClass = $driveConfig['engine'];

            if (!class_exists($driveEngineClass)) {
                throw new \Exception('engine does not exist'. $driveEngineClass);
            }

            $drive = new $driveEngineClass($driveName, $driveConfig['path']);
            return $drive;
        }
    }