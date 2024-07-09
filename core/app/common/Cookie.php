<?php

namespace Avife\common;

class Cookie
{

    public static function setAvifCookie($isAvifSupported)
    {
        if ($isAvifSupported) {
            self::setCookie('browser_avif_support_false', '', time() - (15 * 60));
            self::setCookie('browser_avif_support_true', 'true', time() + 86400); // 24 hours
        } else {
            self::setCookie('browser_avif_support_true', '', time() - (15 * 60));
            self::setCookie('browser_avif_support_false', 'true', time() + 86400); // 24 hours
        }
    }

    private static function setCookie($name, $value, $expire)
    {
        unset($_COOKIE[$name]);
        setcookie($name, $value, $expire, '/', COOKIE_DOMAIN);
    }
}
