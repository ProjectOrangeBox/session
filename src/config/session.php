<?php

/**
 * Session options merged over aplus/session's own defaults, and overridden in turn
 * by anything the caller passes to Session::getInstance().
 *
 * These are restated here rather than inherited for two reasons. The first is
 * correctness: aplus derives 'cookie_secure' from $_SERVER['HTTPS'] /
 * $_SERVER['REQUEST_SCHEME'], but the framework's Input::setGlobals() captures and
 * *unsets* the superglobals during bootstrap, so by the time a session service is
 * constructed $_SERVER is gone and that check silently evaluates to false - a real
 * https request would be handed a session cookie with no Secure flag. (It would
 * also read false behind a TLS-terminating proxy, which sets X-Forwarded-Proto
 * rather than HTTPS.) Pinning the value removes the dependency instead of trying
 * to repair it. The second is that the session cookie is the highest-value cookie
 * the application has, so its protections should be stated where they can be read,
 * not inherited from a transitive dependency's defaults.
 */

declare(strict_types=1);

return [
    // Never send the session cookie over plaintext. A site served over http - a
    // local dev server, typically - must override this to false or the browser
    // will drop the cookie and no session will ever persist.
    'cookie_secure' => true,
    // unreadable from JavaScript, so an XSS cannot lift the session id
    'cookie_httponly' => 1,
    // 'Strict' is the right default for a session cookie: it is not needed to
    // render an inbound link, and withholding it cross-site is a CSRF defense.
    'cookie_samesite' => 'Strict',
    // refuse to adopt a session id the server never issued (session fixation)
    'use_strict_mode' => 1,
    // never accept the session id from the URL, only the cookie
    'use_only_cookies' => 1,
    'use_trans_sid' => 0,
];
