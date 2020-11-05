<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Redirect;

    use Sourcegr\Framework\Base\Helpers\Arr;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class Redirect extends Boom
    {
        public $statusCode = HTTPResponseCode::HTTP_FOUND;


        public function to($url) {
            $this->headers->add('Location', $url);
            return $this;
        }


        public function now() {
            $this->halt = true;
            return $this;
        }


        public function __construct($url = null, $haltsExecution = false, $payload = null, $headers = [])
        {
            $headers = Arr::ensureArray($headers);

            $payload = $payload ?? [];

            parent::__construct($this->statusCode, '', $payload, $haltsExecution);

            $this->headers->add($headers);
            $this->headers->add('Location', $url);
        }


        public function with($flashName, $flashData) {
            return parent::withFlash($flashName, $flashData);
        }
    }