<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem\Engines;


    use Sourcegr\Framework\Base\Interfaces\FileDriveInterface;

    class FileSystemDrive implements FileDriveInterface
    {
        protected $drivePath;
        protected $driveName;


        public function __construct(string $driveName, string $drivePath)
        {
            if (!is_dir($drivePath)) {
                throw new \Exception("Dir does not exist");
//            }else {echo "okkkkkkkkkkkkk $drivePath kkkkkkk";
            }

            $this->drivePath = $drivePath;
            $this->driveName = $driveName;
        }

        public function getRealPath(string $directory = null): string
        {
            return $directory !== null ? $this->drivePath . $directory : $this->drivePath;
        }

        public function getDriveName(): string
        {
            return $this->driveName;
        }

        public function getDrivePath(): string
        {
            return $this->drivePath;
        }

        public function isWritable(string $directory = ''): bool
        {
            return is_writable($this->getRealPath($directory));
        }

        public function isReadable(string $directory = ''): bool
        {
            return is_readable($this->getRealPath($directory));
        }

        public function saveFile(string $filenameWithPath, string $toFilename, string $onDirectory = ''): bool
        {
            return copy($filenameWithPath, $this->getRealPath($onDirectory) . '/', $toFilename);
        }

        public function deleteFile(string $filenameWithPath): bool
        {
            return unlink($this->getRealPath($filenameWithPath));
        }

        public function createDirectory(string $directory, bool $mode = false, bool $recursive = false): bool
        {
            return mkdir($this->getRealPath($directory), $mode, $recursive);
        }

        public function getFileList(string $directory = ''): array
        {
            return array_values(array_filter(glob($this->getRealPath($directory) . '/*'), 'is_file'));
        }

        public function getDirectoryList(string $directory = ''): array
        {
            return array_values(array_filter(glob($this->getRealPath($directory) . '/*'), 'is_dir'));
        }
    }
