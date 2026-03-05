<?php

declare(strict_types=1);

namespace TerminalDetector;

final class TerminalDetector
{
    /**
     * @var array<string, string>
     */
    private const array TERM_PROGRAM_MAP = [
        'iTerm.app' => 'iterm2',
        'iTerm2' => 'iterm2',
        'Apple_Terminal' => 'apple-terminal',
        'vscode' => 'vscode',
        'Hyper' => 'hyper',
        'WarpTerminal' => 'warp',
        'WezTerm' => 'wezterm',
        'rio' => 'rio',
    ];

    public static function detect(): TerminalResult
    {
        $version = self::env('TERM_PROGRAM_VERSION');

        if (($custom = self::env('TERMINAL_DETECTOR')) !== false) {
            return new TerminalResult(true, $custom, $version ?: null);
        }

        $termProgram = self::env('TERM_PROGRAM');

        if ($termProgram !== false && isset(self::TERM_PROGRAM_MAP[$termProgram])) {
            return new TerminalResult(true, self::TERM_PROGRAM_MAP[$termProgram], $version ?: null);
        }

        if (self::env('KITTY_WINDOW_ID') !== false) {
            return new TerminalResult(true, 'kitty', $version ?: null);
        }

        if (self::env('GHOSTTY_RESOURCES_DIR') !== false) {
            return new TerminalResult(true, 'ghostty', $version ?: null);
        }

        if (self::env('WT_SESSION') !== false) {
            return new TerminalResult(true, 'windows-terminal', $version ?: null);
        }

        $terminalEmulator = self::env('TERMINAL_EMULATOR');
        if ($terminalEmulator !== false && str_contains($terminalEmulator, 'JetBrains')) {
            return new TerminalResult(true, 'jetbrains', $version ?: null);
        }

        if (self::env('KONSOLE_DBUS_SESSION') !== false || self::env('KONSOLE_VERSION') !== false) {
            return new TerminalResult(true, 'konsole', $version ?: null);
        }

        if (self::env('TILIX_ID') !== false) {
            return new TerminalResult(true, 'tilix', $version ?: null);
        }

        if (self::env('GNOME_TERMINAL_SCREEN') !== false || self::env('VTE_VERSION') !== false) {
            return new TerminalResult(true, 'gnome-terminal', $version ?: null);
        }

        if (self::env('TABBY_CONFIG_DIRECTORY') !== false) {
            return new TerminalResult(true, 'tabby', $version ?: null);
        }

        if (self::env('TMUX') !== false) {
            return new TerminalResult(true, 'tmux', $version ?: null);
        }

        if (self::env('ZELLIJ') !== false) {
            return new TerminalResult(true, 'zellij', $version ?: null);
        }

        if (self::env('STY') !== false) {
            return new TerminalResult(true, 'screen', $version ?: null);
        }

        if (self::env('SSH_CONNECTION') !== false || self::env('SSH_CLIENT') !== false) {
            return new TerminalResult(true, 'ssh', $version ?: null);
        }

        $term = self::env('TERM');

        if ($term !== false) {
            if ($term === 'alacritty') {
                return new TerminalResult(true, 'alacritty', $version ?: null);
            }

            if ($term === 'xterm-kitty') {
                return new TerminalResult(true, 'kitty', $version ?: null);
            }

            if ($term === 'xterm-ghostty') {
                return new TerminalResult(true, 'ghostty', $version ?: null);
            }

            if (str_starts_with($term, 'xterm')) {
                return new TerminalResult(true, 'xterm', $version ?: null);
            }
        }

        return new TerminalResult(false);
    }

    private static function env(string $name): string|false
    {
        return getenv($name);
    }
}
