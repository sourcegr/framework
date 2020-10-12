<?php

    namespace Sourcegr\Tests\Filesystem\Engines;

    use Sourcegr\Framework\Filesystem\Engines\FileSystemDrive;
    use PHPUnit\Framework\TestCase;

    class FileSystemDriveTest extends TestCase
    {
        const DRIVE_PATH = '/tmp';
        const APPX_DIR = '/appx_test';

        private function init($dir = null) {
            $drive = new FileSystemDrive($dir ?? self::DRIVE_PATH);
            return $drive;
        }

        public function testThrowsOnNonExistingDir() {
            $this->expectException(\Exception::class);
            $drive = $this->init('this_dir_should_not_even_exist_in_more_systems');
        }
        public function testCreateDirectory()
        {
            $drive = $this->init();
            $res = $drive->createDirectory(self::APPX_DIR);
            self::assertEquals(true, $res);

            $drive->deleteDirectory(self::APPX_DIR);
        }

        public function testCreateRecursiveDirectory()
        {
            $drive = $this->init();
            $res = $drive->createDirectory(self::APPX_DIR .'/sub', true);
            self::assertEquals(true, $res);
            $drive->deleteDirectory(self::APPX_DIR .'/sub');
            $drive->deleteDirectory(self::APPX_DIR);
        }

        public function testFailCreateRecursiveDirectoryWithoutRecursive()
        {
            $drive = $this->init();
            $res = $drive->createDirectory(self::APPX_DIR .'/sub');
            self::assertEquals(false, $res);
        }

        public function testIsWritable()
        {
            $drive = $this->init();
            $drive->createDirectory(self::APPX_DIR);
            $res = $drive->isWritable(self::APPX_DIR);

            self::assertEquals(true, $res);

            $drive->deleteDirectory(self::APPX_DIR);
        }

        public function testGetDrivePath()
        {
            $drive = $this->init();
            self::assertEquals(self::DRIVE_PATH, $drive->getDrivePath());
        }

        public function testIsReadable()
        {
            $drive = $this->init();
            self::assertTrue($drive->isReadable());
        }

        /*
         * this works but creates a non self-deletable dir.
         * this is wht it is commented out
         *

        public function testIsNotReadable()
        {
            $drive = $this->init();
            $res = $drive->createDirectory(self::APPX_DIR, false, 0);

            self::assertFalse($drive->isReadable(self::APPX_DIR));
        }
        */


        public function testFailFileExistsOnRoot()
        {
            $drive = $this->init();
            $res = $drive->fileExists('me');
            self::assertFalse($res);
        }

        public function testFileExistsOnRoot()
        {
            $drive = $this->init('/etc');
            $res = $drive->fileExists('hosts');
            self::assertTrue($res);
        }

        public function testFileSaveAndDeleteOnRoot()
        {
            $drive = $this->init();
            $drive->saveFile('/etc/hosts', 'etc_hosts');

            $res = $drive->fileExists('etc_hosts');
            self::assertTrue($res, 'failed fileExists');

            $res = $drive->deleteFile('etc_hosts');
            self::assertTrue($res, 'failed deleteFile');
        }

        public function testFileSaveAndDeleteOnSubdir()
        {
            $drive = $this->init();
            $drive->createDirectory(self::APPX_DIR);
            $drive->saveFile('/etc/hosts', 'etc_hosts', self::APPX_DIR);

            $res = $drive->fileExists('etc_hosts', self::APPX_DIR);
            self::assertTrue($res, 'Error on fileExists');

            $res = $drive->deleteFile(self::APPX_DIR . '/etc_hosts');
            self::assertTrue($res, 'Error on deleteFile');

            $drive->deleteDirectory(self::APPX_DIR);
        }

        public function testGetFileList()
        {
            $drive = $this->init();
            $drive->saveFile('/etc/hosts', 'etc_hosts');
            $list = $drive->getFileList();

            self::assertIsArray($list);
            self::assertContains($drive->getDrivePath().'/etc_hosts', $list);

            $drive->deleteFile('etc_hosts');
        }

        public function testGetDirectoryList()
        {
            $drive = $this->init();
            $drive->createDirectory(self::APPX_DIR);
            $list = $drive->getDirectoryList();

            self::assertIsArray($list);
            self::assertContains($drive->getDrivePath().self::APPX_DIR, $list);

            $drive->deleteDirectory(self::APPX_DIR);
        }
    }
