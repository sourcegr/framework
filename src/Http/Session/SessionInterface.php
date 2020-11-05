<?php

    declare(strict_types=1);

    namespace Sourcegr\Framework\Http\Session;

    use Sourcegr\Framework\Base\Encryptor\EncryptorInterface;

    interface SessionInterface
    {
        public function setEncryptorEngine(EncryptorInterface $encryptor);
        public function getUserIdField();
        public function setTokenName(string $tokenName);
        public function getToken(): string;
        public function regenerateToken();
        public function getTokenName();
        public function setId($id);
        public function start();
        public function regenerate();
        public function loadSession();
        public function getPreviousURL();
        public function setPreviousUrl($url);
        public function all();
        public function forget($keys);
        public function flush();
        public function expireFlashData();
        public function getFlash($name = null);
        public function setFlash($name, $value);
        public function setFreshFlash();
        public function persist();
        public function destroy();
    }

    //
    //    //
    //
    //        public function save()
    //        {
    //            $this->handleFlashData();
    //            $this->engine->save(
    //                $this->getId(),
    //                serialize(Arr::ensureArray($this->parameters))
    //            );
    //            $this->isStarted = false;
    //
    //            return $this;
    //        }
    //
    //        public function handleFlashData () {
    //
    //        }
    //
    //
    //        public function pull($key, $default = null)
    //        {
    //            if ($this->has($key)) {
    //                $val = $this->get($key);
    //                $this->forget($key);
    //                return $val;
    //            } else {
    //                return $default;
    //            }
    //        }
    //
    //
    //
    //        public function put(string $key, $value = null): SessionBag
    //        {
    //            if (!Arr::isArray($key)) {
    //                $key = [$key => $value];
    //            }
    //            $this->add($key);
    //            return $this;
    //        }
    //
    //
    //        public function token(): string
    //        {
    //            return $this->get(self::TOKEN_NAME);
    //        }
    //    //        public function invalidate()
    //        {
    //            $this->flush();
    //            $this->migrate();
    //            // TODO: Implement migrate.
    //            return $this;
    //        }
    //
    //
    //        public function regenerate($destroy = false)
    //        {
    //            $this->migrate($destroy);
    //            $this->regenerateToken();
    //        }
    //
    //
    //        public function migrate($destroy = false)
    //        {
    //            if ($destroy) {
    //                $this->engine->destroy($this->getId());
    //            }
    //            $this->setId(Str::random(40));
    //
    //            return true;
    //        }
    //
    //
    //        public function isStarted()
    //        {
    //            return $this->isStarted;
    //        }
    //
    //
    //    //
    //
    //        public function getEngine()
    //        {
    //            return $this->engine;
    //        }
    //    }