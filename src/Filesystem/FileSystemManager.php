<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem;


    use Sourcegr\Framework\Base\GenericManager;
    use Sourcegr\Framework\Filesystem\Engine\FileSystemDriveInterface;

    class FileSystemManager extends GenericManager implements FileSystemManagerInterface
    {

        public function createDrive(string $driveName, array $driveConfig): FileSystemManagerInterface
        {
            $driveClass = __NAMESPACE__ . "\\Engine\\" . ucfirst(strtolower($driveConfig['engine']));
            $drive = new $driveClass($driveConfig['path']);
            $this->add($driveName, $drive);
            return $this;
        }

        public function attachDrive(string $driveName, FileSystemDriveInterface $drive): FileSystemManagerInterface
        {
            $this->add($driveName, $drive);
            return $this;
        }


        /**
         * @param string $driveName
         *
         * @return FileSystemDriveInterface|null
         * @throws \Exception
         */
        public function drive(string $driveName): ?FileSystemDriveInterface
        {

            $drive = $this->get($driveName) ?? null;

            if ($drive === null) {
                throw new \Exception('Drive does not exist');
            }
            return $drive;
        }
    }