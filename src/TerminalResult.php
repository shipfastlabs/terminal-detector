<?php

declare(strict_types=1);

namespace TerminalDetector;

final readonly class TerminalResult
{
    public function __construct(
        public bool $detected,
        public ?string $name = null,
        public ?string $version = null,
    ) {}

    public function knownTerminal(): ?KnownTerminal
    {
        return KnownTerminal::tryFrom($this->name ?? '');
    }
}
