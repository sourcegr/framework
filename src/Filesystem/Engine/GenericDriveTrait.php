<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Filesystem\Engine;


    Trait GenericDriveTrait
    {
        public function getDrivePath(): string
        {
            return $this->drivePath;
        }

        public function createUniqFilename($originalFilename)
        {
            $filename = $originalFilename;
            $counter = 0;
            while ($this->fileExists($filename)) {
                $counter++;
                $filename = $counter . '.' . $originalFilename;
            }
            return $filename;
        }
    }