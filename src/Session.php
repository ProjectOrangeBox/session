<?php

declare(strict_types=1);

namespace orange\session;

use Framework\Session\SaveHandler;
use Framework\Session\Session as aplusSession;

class Session extends aplusSession implements SessionInterface
{
    private static self $instance;

    /**
     * @param array<string, int|string|bool> $options
     */
    public function __construct(array $options = [], ?SaveHandler $handler = null)
    {
        // three layers, each overriding the one before: aplus's defaults, this
        // package's hardened ones, then whatever the caller asked for. See
        // config/session.php for why the security-relevant options are pinned
        // here instead of being inherited.
        parent::__construct(array_replace(self::secureDefaults(), $options), $handler);
    }

    /**
     * This package's session option defaults.
     *
     * @return array<string, int|string|bool>
     */
    public static function secureDefaults(): array
    {
        return require __DIR__ . '/config/session.php';
    }

    /**
     * @param array<string, int|string|bool> $options
     */
    public static function getInstance(array $options = [], ?SaveHandler $handler = null): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($options, $handler);
        }

        return self::$instance;
    }
}
