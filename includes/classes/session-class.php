<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');
class Sessionclass{

    public function __construct(?string $cacheExpire = null, ?string $cacheLimiter = null)
    {
        if (session_status() === PHP_SESSION_NONE) {

            if ($cacheLimiter !== null) {
                session_cache_limiter($cacheLimiter);
            }

            if ($cacheExpire !== null) {
                session_cache_expire($cacheExpire);
            }

            session_start();
        }
    }

    public function get(string $key)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return null;
    }

    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function remove(string $key)
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function clear()
    {
        session_unset();
    }

    public function has(string $key)
    {
        return array_key_exists($key, $_SESSION);
    }
}
?>