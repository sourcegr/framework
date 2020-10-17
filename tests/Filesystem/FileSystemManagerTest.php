<?php

    namespace Sourcegr\Tests\Filesystem;

    use Sourcegr\Framework\Filesystem\Engine\Local;
    use Sourcegr\Framework\Filesystem\FileSystemManager;
    use PHPUnit\Framework\TestCase;

    class FileSystemManagerTest extends TestCase
    {
        const DRIVE_NAME = 'tmp';
        const DRIVE_PATH = '/tmp';


        public function testThrowsOnNonExistingDrive() {
            $manager = new FileSystemManager();
            $this->expectException(\Exception::class);
            $manager->drive('oops');
        }


        public function testAttachDrive() {
            $manager = new FileSystemManager();
            $drive = new Local(self::DRIVE_PATH);

            $manager->attachDrive(self::DRIVE_NAME, $drive);

            $this->assertCount(1, $manager->getDrives());
            $this->assertContains($drive, $manager->getDrives());

            return $manager;
        }

        public function testGetDrive() {
            $manager = $this->testAttachDrive();
            $drive = $manager->drive(self::DRIVE_NAME);

            $this->assertInstanceOf(Local::class, $drive);
        }

        public function testDrivePath() {
            $manager = $this->testAttachDrive();
            $drive = $manager->drive(self::DRIVE_NAME);

            $this->assertEquals(self::DRIVE_PATH, $drive->getDrivePath());
        }
    }
