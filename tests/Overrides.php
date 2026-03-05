<?php

declare(strict_types=1);

namespace TerminalDetector;

function getenv(string $name): string|false
{
    if (isset($GLOBALS['__mock_getenv'])) {
        /** @var array<string, string|false> $mock */
        $mock = $GLOBALS['__mock_getenv'];

        return $mock[$name] ?? false;
    }

    return \getenv($name);
}
