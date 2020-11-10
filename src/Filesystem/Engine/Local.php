<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem\Engine;


    class Local implements FileSystemDriveInterface
    {
        use GenericDriveTrait;

        protected $drivePath;

        protected function getRealPath(string $directory = null): string
        {
            $dir = $directory !== null ? $this->drivePath . $directory : $this->drivePath;
            return $dir . '/';
        }


        public function __construct(string $drivePath)
        {
            if (!is_dir($drivePath)) {
                throw new \Exception("Dir does not exist");
            }

            $this->drivePath = $drivePath;
        }


        public function isWritable(string $directory = ''): bool
        {
            return is_writable($this->getRealPath($directory));
        }


        public function isReadable(string $directory = ''): bool
        {
            return is_readable($this->getRealPath($directory));
        }


        public function saveFile(string $filenameWithPath, string $toFilename, string $onDirectory = '')
        {
            if (@copy($filenameWithPath, $this->getRealPath($onDirectory) . $toFilename)) {
                return $toFilename;
            }else {
                return false;
            }
        }


        public function saveFileUniq(string $filenameWithPath, string $toFilename, string $onDirectory = '')
        {
            $f = $this->createUniqFilename($toFilename);
            return $this->saveFile($filenameWithPath, $f, $onDirectory);
        }


        public function deleteFile(string $filename, string $onDirectory = ''): bool
        {
            return @unlink($this->getRealPath($onDirectory) . $filename);
        }


        public function createDirectory(string $directory, bool $recursive = false, int $mode = 0755): bool
        {
            $dir = $this->getRealPath($directory);
            return @mkdir($dir, $mode, $recursive);
        }


        public function getFileList(string $directory = ''): array
        {
            return array_values(array_filter(glob($this->getRealPath($directory) . '*'), 'is_file'));
        }


        public function getDirectoryList(string $directory = ''): array
        {
            return array_values(array_filter(glob($this->getRealPath($directory) . '*'), 'is_dir'));
        }


        public function deleteDirectory(string $directory, bool $recursive = false): bool
        {
            $dir = $this->getRealPath($directory);
            if (!$recursive) {
                return @rmdir($dir);
            }
            // todo add recursive delete
        }


        public function fileExists(string $file, string $directory = ''): bool
        {
            $dir = $this->getRealPath($directory);
            return file_exists($dir . $file);
        }
    }
