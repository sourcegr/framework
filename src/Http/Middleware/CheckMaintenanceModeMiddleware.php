<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Middleware;


    use Sourcegr\Framework\App\AppInterface;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Response\HTTPResponseCodes;

    class CheckMaintenanceModeMiddleware extends BaseMiddleware
    {
        public $redirectTo = null;

        public $allowedUrls = [
            '/^allowed.*/'
        ];


        public function handle(AppInterface $app, MaintenanceGate $gate)
        {
            if (!$app->isDownForMaintenance()) {
                return;
            }

            if ($gate->allows($this->allowedUrls)) {
                return;
            }

            // if redirectTo is set, send a Temporary Redirect
            if ($this->redirectTo) {
                throw new BoomException(new Boom(HTTPResponseCodes::HTTP_TEMPORARY_REDIRECT));
            }

            // let the gate do what it should do
            //$gate->sendMaintainanceSignal();

            // or send Service Anavailable
             throw new BoomException(new Boom(HTTPResponseCodes::HTTP_SERVICE_UNAVAILABLE));
        }
    }