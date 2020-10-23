<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Redirect;

    use Sourcegr\Framework\Base\Helpers\Arr;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class Redirect extends Boom
    {
        public $statusCode = HTTPResponseCode::HTTP_TEMPORARY_REDIRECT;

        public function __construct($url, $immediate = false, $payload = null, $headers = [])
        {
            $headers = Arr::ensureArray($headers);

            $payload = $payload ?? [];

            if (!is_null($immediate)) {
                $payload['immediate'] = $immediate;
            }

            parent::__construct($this->statusCode, $payload);

            $this->headers->add($headers);
            $this->headers->add('Location', $url);
        }
    }