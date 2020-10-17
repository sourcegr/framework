<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session\Drivers;


    use Sourcegr\Framework\Http\Session\SessionManager;

    class NativeSessionDriver extends SessionManager
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
    }