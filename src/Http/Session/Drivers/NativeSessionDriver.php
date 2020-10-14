<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session\Drivers;


    use Sourcegr\Framework\Http\Session\SessionHandler;

    class NativeSessionDriver extends SessionHandler
    {
        protected $config;

        public function getID() {
            return session_id();
        }

        public function clear($init = null)
        {
//            unset($_SESSION);
            $_SESSION = $init;
            return $this;
        }

        public function __construct($config)
        {
            $this->config = $config;
            $this->setCookieParams();
        }

        public function set($key, $val)
        {
            $_SESSION[$key] = $val;
        }

        public function get($key)
        {
            return ($_SESSION[$key] ?? null);
        }

        public function regenerateID()
        {
            session_regenerate_id();
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
    }