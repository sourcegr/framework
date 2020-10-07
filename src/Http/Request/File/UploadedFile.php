<?php


    namespace Sourcegr\Framework\Http\Request\File;


    class UploadedFile extends SimpleFile
    {
        protected $file;
        /**
         * UploadedFile constructor.
         *
         * @param $file
         */

        public $name;
        public $type;
        public $tmp_name;
        public $error;
        public $size;


        public function __construct($file)
        {
            $valid = isset($file['name']) && isset($file['type']) && isset($file['tmp_name']) && isset($file['error']) && isset($file['size']);
            if (!$valid) {
                throw new \Exception("invalid file");
            }

            $this->name = $file['name'];
            $this->type = $file['type'];
            $this->tmp_name = $file['tmp_name'];
            $this->error = $file['error'];
            $this->size = $file['size'];

            $pathParts = pathinfo($file['tmp_name']);
        }

        public function moveTo($newPath, $newFilename = null) {

        }
    }