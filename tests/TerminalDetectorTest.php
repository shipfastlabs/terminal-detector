<?php

declare(strict_types=1);

use TerminalDetector\KnownTerminal;
use TerminalDetector\TerminalDetector;
use TerminalDetector\TerminalResult;

use function TerminalDetector\detectTerminal;

beforeEach(function (): void {
    $GLOBALS['__mock_getenv'] = [];
});

afterEach(function (): void {
    unset($GLOBALS['__mock_getenv']);
});

it('returns not detected when no env vars are set', function (): void {
    $result = TerminalDetector::detect();

    expect($result->detected)->toBeFalse()
        ->and($result->name)->toBeNull()
        ->and($result->version)->toBeNull()
        ->and($result->knownTerminal())->toBeNull();
});

it('detects custom terminal via TERMINAL_DETECTOR env var', function (): void {
    $GLOBALS['__mock_getenv'] = [
        'TERMINAL_DETECTOR' => 'my-custom-terminal',
    ];

    $result = TerminalDetector::detect();

    expect($result->detected)->toBeTrue()
        ->and($result->name)->toBe('my-custom-terminal')
        ->and($result->knownTerminal())->toBeNull();
});

it('detects terminals via TERM_PROGRAM', function (string $termProgram, KnownTerminal $expected): void {
    $GLOBALS['__mock_getenv'] = [
        'TERM_PROGRAM' => $termProgram,
    ];

    $result = TerminalDetector::detect();

    expect($result->detected)->toBeTrue()
        ->and($result->knownTerminal())->toBe($expected);
})->with([
    ['iTerm.app', KnownTerminal::ITerm2],
    ['iTerm2', KnownTerminal::ITerm2],
    ['Apple_Terminal', KnownTerminal::AppleTerminal],
    ['vscode', KnownTerminal::VSCode],
    ['Hyper', KnownTerminal::Hyper],
    ['WarpTerminal', KnownTerminal::WarpTerminal],
    ['WezTerm', KnownTerminal::WezTerm],
    ['rio', KnownTerminal::Rio],
]);

it('captures version from TERM_PROGRAM_VERSION', function (): void {
    $GLOBALS['__mock_getenv'] = [
        'TERM_PROGRAM' => 'iTerm.app',
        'TERM_PROGRAM_VERSION' => '3.5.0',
    ];

    $result = TerminalDetector::detect();

    expect($result->version)->toBe('3.5.0');
});

it('detects terminals via dedicated env vars', function (array $env, KnownTerminal $expected): void {
    $GLOBALS['__mock_getenv'] = $env;

    $result = TerminalDetector::detect();

    expect($result->detected)->toBeTrue()
        ->and($result->knownTerminal())->toBe($expected);
})->with([
    [['KITTY_WINDOW_ID' => '1'], KnownTerminal::Kitty],
    [['GHOSTTY_RESOURCES_DIR' => '/usr/share/ghostty'], KnownTerminal::Ghostty],
    [['WT_SESSION' => '{guid}'], KnownTerminal::WindowsTerminal],
    [['TERMINAL_EMULATOR' => 'JetBrains-JediTerm'], KnownTerminal::JetBrains],
    [['KONSOLE_DBUS_SESSION' => '/Sessions/1'], KnownTerminal::Konsole],
    [['KONSOLE_VERSION' => '221201'], KnownTerminal::Konsole],
    [['TILIX_ID' => 'abc123'], KnownTerminal::Tilix],
    [['GNOME_TERMINAL_SCREEN' => '/org/gnome/Terminal'], KnownTerminal::GnomeTerminal],
    [['VTE_VERSION' => '6800'], KnownTerminal::GnomeTerminal],
    [['TABBY_CONFIG_DIRECTORY' => '/home/user/.config/tabby'], KnownTerminal::Tabby],
]);

it('detects multiplexers', function (array $env, KnownTerminal $expected): void {
    $GLOBALS['__mock_getenv'] = $env;

    $result = TerminalDetector::detect();

    expect($result->detected)->toBeTrue()
        ->and($result->knownTerminal())->toBe($expected);
})->with([
    [['TMUX' => '/tmp/tmux-1000/default,12345,0'], KnownTerminal::Tmux],
    [['ZELLIJ' => '0'], KnownTerminal::Zellij],
    [['STY' => '12345.pts-0.host'], KnownTerminal::Screen],
]);

it('detects SSH sessions', function (array $env): void {
    $GLOBALS['__mock_getenv'] = $env;

    $result = TerminalDetector::detect();

    expect($result->detected)->toBeTrue()
        ->and($result->knownTerminal())->toBe(KnownTerminal::SSHSession);
})->with([
    [['SSH_CONNECTION' => '192.168.1.1 12345 192.168.1.2 22']],
    [['SSH_CLIENT' => '192.168.1.1 12345 22']],
]);

it('detects terminals via TERM fallback', function (string $term, KnownTerminal $expected): void {
    $GLOBALS['__mock_getenv'] = [
        'TERM' => $term,
    ];

    $result = TerminalDetector::detect();

    expect($result->detected)->toBeTrue()
        ->and($result->knownTerminal())->toBe($expected);
})->with([
    ['alacritty', KnownTerminal::Alacritty],
    ['xterm-kitty', KnownTerminal::Kitty],
    ['xterm-ghostty', KnownTerminal::Ghostty],
    ['xterm', KnownTerminal::Xterm],
    ['xterm-256color', KnownTerminal::Xterm],
]);

it('prioritizes TERMINAL_DETECTOR over TERM_PROGRAM', function (): void {
    $GLOBALS['__mock_getenv'] = [
        'TERMINAL_DETECTOR' => 'custom',
        'TERM_PROGRAM' => 'iTerm.app',
    ];

    $result = TerminalDetector::detect();

    expect($result->name)->toBe('custom');
});

it('prioritizes TERM_PROGRAM over dedicated env vars', function (): void {
    $GLOBALS['__mock_getenv'] = [
        'TERM_PROGRAM' => 'vscode',
        'KITTY_WINDOW_ID' => '1',
    ];

    $result = TerminalDetector::detect();

    expect($result->knownTerminal())->toBe(KnownTerminal::VSCode);
});

it('prioritizes tilix over gnome-terminal when both vars are present', function (): void {
    $GLOBALS['__mock_getenv'] = [
        'TILIX_ID' => 'abc',
        'VTE_VERSION' => '6800',
    ];

    $result = TerminalDetector::detect();

    expect($result->knownTerminal())->toBe(KnownTerminal::Tilix);
});

it('works via the helper function', function (): void {
    $GLOBALS['__mock_getenv'] = [
        'TERM_PROGRAM' => 'WarpTerminal',
    ];

    $result = detectTerminal();

    expect($result)->toBeInstanceOf(TerminalResult::class)
        ->and($result->detected)->toBeTrue()
        ->and($result->knownTerminal())->toBe(KnownTerminal::WarpTerminal);
});

it('returns null version when TERM_PROGRAM_VERSION is not set', function (): void {
    $GLOBALS['__mock_getenv'] = [
        'TERM_PROGRAM' => 'vscode',
    ];

    $result = TerminalDetector::detect();

    expect($result->version)->toBeNull();
});

it('resolves known terminal from result', function (): void {
    $result = new TerminalResult(true, 'ghostty', '1.0.0');

    expect($result->knownTerminal())->toBe(KnownTerminal::Ghostty);
});

it('returns null for unknown terminal name', function (): void {
    $result = new TerminalResult(true, 'some-unknown-terminal');

    expect($result->knownTerminal())->toBeNull();
});
