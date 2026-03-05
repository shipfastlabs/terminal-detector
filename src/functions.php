<?php

declare(strict_types=1);

namespace TerminalDetector;

function detectTerminal(): TerminalResult
{
    return TerminalDetector::detect();
}
