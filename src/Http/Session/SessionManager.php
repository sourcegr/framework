<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session;


    class SessionManager implements SessionManagerInterface
    {
        const FLASH_OLD = 'flash.old';
        const FLASH_NEW = 'flash.new';

        protected $handler;

        public function getID()
        {
            return $this->handler->getID();
        }

        public function get($key)
        {
            return $this->handler->get($key);
        }

        public function __construct($configName)
        {
        }

        public function setCookieParams()
        {
            if ($this->config['autostart']) {
                session_name($this->config['cookie']);
                $existing = session_get_cookie_params();
                session_set_cookie_params(
                    $this->config['lifetime'] * 60,
                    $this->config['path'] ?? $existing['path'],
                    $this->config['domain'] ?? $existing['domain'],
                    $this->config['secure'] ?? $existing['secure'],
                    $this->config['http_only'] ?? $existing['http_only'],
                );
                session_start();
            }
        }

        public function ageSession()
        {
            $newFlash = $this->handler->get(self::FLASH_NEW);
            $this->handler->set(self::FLASH_OLD, $newFlash);
            $this->handler->set(self::FLASH_NEW, []);
        }

        public function clear()
        {
            $this->handler->clear([
                self::FLASH_OLD => [],
                self::FLASH_NEW => [],
            ]);

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