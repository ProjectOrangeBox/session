<?php

declare(strict_types=1);

use orange\session\Session;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    /**
     * Read the merged option array off an instance - aplus keeps it protected.
     *
     * @return array<string, mixed>
     */
    private function optionsOf(Session $session): array
    {
        return new ReflectionProperty($session, 'options')->getValue($session);
    }

    public function testSecureDefaultsAreHardened(): void
    {
        $defaults = Session::secureDefaults();

        $this->assertTrue($defaults['cookie_secure']);
        $this->assertSame(1, $defaults['cookie_httponly']);
        $this->assertSame('Strict', $defaults['cookie_samesite']);
        $this->assertSame(1, $defaults['use_strict_mode']);
        $this->assertSame(1, $defaults['use_only_cookies']);
        $this->assertSame(0, $defaults['use_trans_sid']);
    }

    /**
     * The bug this pins: aplus derives cookie_secure from $_SERVER['HTTPS'] /
     * $_SERVER['REQUEST_SCHEME'], but the framework's Input::setGlobals() has
     * captured and unset the superglobals long before a session service is built,
     * so that check always saw nothing and produced a session cookie with no
     * Secure flag - on https requests included. Here neither key is present,
     * which is exactly the state that used to yield false.
     */
    public function testSecureFlagDoesNotDependOnTheServerSuperglobal(): void
    {
        $https = $_SERVER['HTTPS'] ?? null;
        $scheme = $_SERVER['REQUEST_SCHEME'] ?? null;
        unset($_SERVER['HTTPS'], $_SERVER['REQUEST_SCHEME']);

        try {
            $this->assertTrue($this->optionsOf(new Session())['cookie_secure']);
        } finally {
            if ($https !== null) {
                $_SERVER['HTTPS'] = $https;
            }
            if ($scheme !== null) {
                $_SERVER['REQUEST_SCHEME'] = $scheme;
            }
        }
    }

    /**
     * Hardened defaults are a floor, not a cage - a site served over plain http
     * has to be able to turn Secure back off or the browser drops the cookie and
     * no session ever persists.
     */
    public function testCallerOptionsOverrideTheDefaults(): void
    {
        $options = $this->optionsOf(new Session(['cookie_secure' => false, 'cookie_samesite' => 'Lax']));

        $this->assertFalse($options['cookie_secure']);
        $this->assertSame('Lax', $options['cookie_samesite']);

        // anything not overridden still comes from this package's defaults
        $this->assertSame(1, $options['cookie_httponly']);
    }

    /**
     * aplus's own defaults still fill in everything this package has no opinion
     * about, rather than being replaced wholesale.
     */
    public function testAplusDefaultsSurviveForUnspecifiedOptions(): void
    {
        $options = $this->optionsOf(new Session());

        $this->assertArrayHasKey('name', $options);
        $this->assertArrayHasKey('cookie_lifetime', $options);
        $this->assertSame('/', $options['cookie_path']);
    }
}
