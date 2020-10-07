<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session;


    class SessionHandler
    {
        const FLASH_OLD = 'flash.old';
        const FLASH_NEW = 'flash.new';

        protected $handler;

        public function __construct($app, $handler)
        {
            $app->registerShutdownCallback(
                function () use ($handler) {
                    $newFlash = $handler->get(self::FLASH_NEW);
                    $handler->set(self::FLASH_OLD, $newFlash);
                    $handler->set(self::FLASH_NEW, []);
                }
            );
            $this->handler = $handler;
        }

        public function clear()
        {
            $this->handler->clear(
                [
                    self::FLASH_OLD => [],
                    self::FLASH_NEW => [],
                ]
            );

            return $this;
        }

        public function regenerateID()
        {
            $this->handler->regenerateID();
            return $this;
        }

        public function addFlash($key, $value)
        {
            $flash = $this->handler->get(self::FLASH_NEW);
            $flash[$key] = $value;
            $this->handler->set(self::FLASH_NEW, $flash);
            return $this;
        }

        public function clearFlash()
        {
            $this->handler->set(self::FLASH_NEW, []);
            return $this;
        }

        public function clearFlashItem($key)
        {
            $flash = $this->handler->get(self::FLASH_NEW);

            if (isset($flash[$key])) {
                unset ($flash[$key]);
                $this->handler->set(self::FLASH_NEW, $flash);
            }

            return $this;
        }
    }